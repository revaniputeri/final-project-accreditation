<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PPenelitianModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PPenelitianDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PPenelitianController extends Controller
{
    public function index(PPenelitianDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        // Distribusi Skema Penelitian (pie chart)
        $skemaDistribution = PPenelitianModel::select('skema', DB::raw('count(*) as total'))
            ->groupBy('skema')
            ->orderBy('total', 'desc')
            ->get();

        // Tren Penelitian per Tahun (line chart)
        $trenPerTahun = PPenelitianModel::select('tahun', DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Peran Dosen dalam Penelitian (doughnut chart)
        $peranDistribution = PPenelitianModel::select('peran', DB::raw('count(*) as total'))
            ->groupBy('peran')
            ->orderBy('total', 'desc')
            ->get();

        // Keterlibatan Mahasiswa S2 (bar chart)
        $mahasiswaS2Distribution = PPenelitianModel::select('melibatkan_mahasiswa_s2', DB::raw('count(*) as total'))
            ->groupBy('melibatkan_mahasiswa_s2')
            ->orderBy('melibatkan_mahasiswa_s2')
            ->get();

        // Tren dana untuk penelitian per tahun per skema (multi-line chart)
        $trenDanaPerTahunPerSkema = PPenelitianModel::select('tahun', 'skema', DB::raw('sum(dana) as total_dana'))
            ->groupBy('tahun', 'skema')
            ->orderBy('tahun')
            ->orderBy('skema')
            ->get();

        // Prepare data arrays for charts
        $skemaLabels = $skemaDistribution->pluck('skema');
        $skemaData = $skemaDistribution->pluck('total');

        $trenLabels = $trenPerTahun->pluck('tahun');
        $trenData = $trenPerTahun->pluck('total');

        $peranLabels = $peranDistribution->pluck('peran');
        $peranData = $peranDistribution->pluck('total');

        $mahasiswaS2Labels = $mahasiswaS2Distribution->map(function ($item) {
            return $item->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak';
        });
        $mahasiswaS2Data = $mahasiswaS2Distribution->pluck('total');

        // Prepare multi-line chart data
        $skemaList = $skemaLabels->toArray();
        $years = $trenLabels->toArray();

        $multiLineDataSets = [];
        foreach ($skemaList as $skema) {
            $dataPerYear = [];
            foreach ($years as $year) {
                $found = $trenDanaPerTahunPerSkema->firstWhere(function ($item) use ($year, $skema) {
                    return $item->tahun == $year && $item->skema == $skema;
                });
                $dataPerYear[] = $found ? (float) $found->total_dana : 0;
            }
            $multiLineDataSets[] = [
                'label' => $skema,
                'data' => $dataPerYear,
                'fill' => false,
                'borderColor' => null, // will be set in JS
                'tension' => 0.3,
            ];
        }

        return $dataTable->render('portofolio.penelitian.index', compact(
            'isAdm', 'isAng', 'isDos',
            'skemaLabels', 'skemaData',
            'trenLabels', 'trenData',
            'peranLabels', 'peranData',
            'mahasiswaS2Labels', 'mahasiswaS2Data',
            'multiLineDataSets'
        ));
    }

    private function generateUniqueFilename($directory, $filename)
    {
        $filePath = $directory . '/' . $filename;
        $fileInfo = pathinfo($filename);
        $name = $fileInfo['filename'];
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
        $counter = 1;

        while (Storage::exists($filePath)) {
            $filePath = $directory . '/' . $name . '_' . $counter . $extension;
            $counter++;
        }

        return basename($filePath);
    }

    public function create_ajax()
    {
        $dosens = UserModel::whereHas('level', function ($query) {
            $query->where('kode_level', 'DOS');
        })->get();

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        return view('portofolio.penelitian.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'judul_penelitian' => 'required|string|max:255',
                'skema' => 'required|string|max:100',
                'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                'dana' => 'required|numeric|min:0',
                'peran' => 'required|in:ketua,anggota',
                'melibatkan_mahasiswa_s2' => 'required|boolean',
                'bukti' => $role === 'DOS' ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];

            if ($role === 'ADM') {
                $rules['nidn'] = 'required|string|exists:profile_user,nidn';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                if ($role === 'ADM') {
                    $nidn = $request->input('nidn');
                    $profileUser = DB::table('profile_user')->where('nidn', $nidn)->first();

                    if (!$profileUser) {
                        return response()->json([
                            'status' => false,
                            'alert' => 'error',
                            'message' => 'NIDN tidak ditemukan di data profil user',
                        ]);
                    }

                    $id_user = $profileUser->id_user;
                } elseif ($role === 'DOS') {
                    $id_user = $user->id_user;
                } else {
                    $id_user = $user->id_user;
                }

                if (!$id_user) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'ID user tidak ditemukan. Pastikan akun user terkait.',
                    ]);
                }

                $exists = PPenelitianModel::where('id_user', $id_user)
                    ->where('judul_penelitian', $request->input('judul_penelitian'))
                    ->where('tahun', $request->input('tahun'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Judul Penelitian yang sama pada tahun yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul_penelitian',
                    'skema',
                    'tahun',
                    'dana',
                    'peran',
                    'melibatkan_mahasiswa_s2',
                ]);

                $data['id_user'] = $id_user;

                if ($role === 'DOS') {
                    $data['status'] = 'tervalidasi';
                    $data['sumber_data'] = 'dosen';
                } elseif ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                    $data['sumber_data'] = 'p3m';
                } else {
                    $data['status'] = $request->input('status', 'perlu validasi');
                    $data['sumber_data'] = $request->input('sumber_data', 'p3m');
                }

                if ($request->hasFile('bukti')) {
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($role === 'ADM' && isset($nidn)) {
                        $nidnPrefix = $nidn . '_';
                    } elseif ($role === 'DOS') {
                        $profileUser = DB::table('profile_user')->where('id_user', $user->id_user)->first();
                        $nidnPrefix = $profileUser && $profileUser->nidn ? $profileUser->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/penelitian', $filename);
                    $path = $file->storeAs('public/portofolio/penelitian', $filename);
                    $data['bukti'] = $filename;
                }

                PPenelitianModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data penelitian berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data penelitian',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function edit_ajax($id)
    {
        $dosens = UserModel::whereHas('level', function ($query) {
            $query->where('kode_level', 'DOS');
        })->get();

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        $penelitian = PPenelitianModel::findOrFail($id);
        return view('portofolio.penelitian.edit_ajax', compact('penelitian', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'judul_penelitian' => 'required|string|max:255',
                    'skema' => 'required|string|max:100',
                    'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'dana' => 'required|numeric|min:0',
                    'peran' => 'required|in:ketua,anggota',
                    'melibatkan_mahasiswa_s2' => 'required|boolean',
                    'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ];

                if ($role === 'ADM') {
                    $rules['nidn'] = 'required|string|exists:profile_user,nidn';
                }

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    Log::error('Validation failed', $validator->errors()->toArray());
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors(),
                    ]);
                }

                $penelitian = PPenelitianModel::findOrFail($id);

                try {
                    if ($role === 'ADM') {
                        $nidn = $request->input('nidn');
                        $profileUser = DB::table('profile_user')->where('nidn', $nidn)->first();

                        if (!$profileUser) {
                            return response()->json([
                                'status' => false,
                                'alert' => 'error',
                                'message' => 'NIDN tidak ditemukan di data profil user',
                            ]);
                        }

                        $id_user = $profileUser->id_user;
                    } elseif ($role === 'DOS') {
                        $id_user = $user->id_user;
                    } else {
                        $id_user = $user->id_user;
                    }

                    if (!$id_user) {
                        return response()->json([
                            'status' => false,
                            'alert' => 'error',
                            'message' => 'ID user tidak ditemukan. Pastikan akun user terkait.',
                        ]);
                    }

                    // Custom duplicate check for id_user and judul_penelitian excluding current record
                    $exists = PPenelitianModel::where('id_user', $id_user)
                        ->where('judul_penelitian', $request->input('judul_penelitian'))
                        ->where('tahun', $request->input('tahun'))
                        ->where('id_penelitian', '!=', $id)
                        ->exists();

                    if ($exists) {
                        return response()->json([
                            'status' => false,
                            'alert' => 'error',
                            'message' => 'Data dengan NIDN dan Judul Penelitian yang sama pada tahun yang sama sudah ada.',
                        ]);
                    }

                    $data = $request->only([
                        'judul_penelitian',
                        'skema',
                        'tahun',
                        'dana',
                        'peran',
                        'melibatkan_mahasiswa_s2',
                    ]);

                    if ($role === 'ADM') {
                        $data['status'] = 'perlu validasi';
                    }

                    if ($request->hasFile('bukti')) {
                        if ($penelitian->bukti && Storage::exists('public/portofolio/penelitian/' . $penelitian->bukti)) {
                            Storage::delete('public/portofolio/penelitian/' . $penelitian->bukti);
                        }
                        $file = $request->file('bukti');
                        $nidnPrefix = '';
                        if ($penelitian->user && $penelitian->user->profile) {
                            $nidnPrefix = $penelitian->user->profile->nidn ? $penelitian->user->profile->nidn . '_' : '';
                        }
                        $originalName = $file->getClientOriginalName();
                        $filename = $nidnPrefix . $originalName;
                        $filename = $this->generateUniqueFilename('public/portofolio/penelitian', $filename);
                        $path = $file->storeAs('public/portofolio/penelitian', $filename);
                        $data['bukti'] = $filename;
                    }

                    $penelitian->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data penelitian berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data penelitian',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function confirm_ajax($id)
    {
        $penelitian = PPenelitianModel::findOrFail($id);
        return view('portofolio.penelitian.confirm_ajax', compact('penelitian'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $penelitian = PPenelitianModel::findOrFail($id);

        try {
            if ($penelitian->bukti && Storage::exists('public/portofolio/penelitian/' . $penelitian->bukti)) {
                Storage::delete('public/portofolio/penelitian/' . $penelitian->bukti);
            }
            $penelitian->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in delete_ajax: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function detail_ajax($id)
    {
        $penelitian = PPenelitianModel::with('user.profile')->findOrFail($id);
        return view('portofolio.penelitian.detail_ajax', compact('penelitian'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $penelitian = PPenelitianModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $penelitian->status = $request->input('status');
            $penelitian->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.penelitian.validasi_ajax', compact('penelitian'));
    }

    public function import()
    {
        return view('portofolio.penelitian.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_penelitian' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_penelitian');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $insertData = [];
            $skippedData = [];
            $errors = [];

            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;
            $userNidn = DB::table('profile_user')->where('id_user', $user->id_user)->value('nidn');

            foreach ($data as $row => $values) {
                if ($row == 1) continue;

                $nidn = trim($values['A']);
                $judulPenelitian = trim($values['B']);
                $tahun = trim($values['D']);
                $skema = strtolower(trim($values['C']));
                $dana = trim($values['E']);
                $peran = strtolower(trim($values['F']));
                $melibatkanMhsS2 = strtolower(trim($values['G']));

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $user = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$user) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                $isDuplicate = PPenelitianModel::where('id_user', $user->id_user)
                    ->where('judul_penelitian', $judulPenelitian)
                    ->where('tahun', $tahun)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn, Judul Penelitian $judulPenelitian dan Tahun $tahun sudah ada.";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $user->id_user,
                    'judul_penelitian' => $judulPenelitian,
                    'skema' => $skema,
                    'tahun' => $tahun,
                    'dana' => $dana,
                    'peran' => $peran,
                    'melibatkan_mahasiswa_s2' => in_array($melibatkanMhsS2, ['true', '1', 'yes', 'ya']) ? true : false,
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'judul_penelitian' => 'required|string|max:255',
                    'skema' => 'required|string|max:100',
                    'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'dana' => 'required|numeric|min:0',
                    'peran' => 'required|in:ketua,anggota',
                    'melibatkan_mahasiswa_s2' => 'required|boolean',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $user->id_user,
                    'judul_penelitian' => $judulPenelitian,
                    'skema' => $skema,
                    'tahun' => $tahun,
                    'dana' => $dana,
                    'peran' => $peran,
                    'melibatkan_mahasiswa_s2' => in_array($melibatkanMhsS2, ['true', '1', 'yes', 'ya']) ? true : false,
                    'status' => $role === 'DOS' ? 'tervalidasi' : 'perlu validasi',
                    'sumber_data' => $role === 'DOS' ? 'dosen' : 'p3m',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $allMessages = array_merge($skippedData, $errors);

            if (empty($insertData)) {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Tidak ada data baru yang valid untuk diimport:' .
                        "\n" . implode("\n", array_slice($allMessages, 0, 1)) .
                        (count($allMessages) > 3 ? "\n...dan " . (count($allMessages) - 3) . " lainnya." : ''),
                    'showConfirmButton' => true
                ], 422);
            }

            $insertedCount = PPenelitianModel::insert($insertData);

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Import data berhasil',
                'inserted_count' => $insertedCount,
                'skipped_count' => count($skippedData),
                'info' => $allMessages,
                'error_count' => count($errors)
            ]);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file',
                'error' => $e->getMessage(),
                'showConfirmButton' => true
            ], 500);
        }
    }

    public function export_excel()
    {
        $query = PPenelitianModel::join('user', 'p_penelitian.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_penelitian.judul_penelitian',
                'p_penelitian.skema',
                'p_penelitian.tahun',
                'p_penelitian.dana',
                'p_penelitian.peran',
                'p_penelitian.melibatkan_mahasiswa_s2',
                'p_penelitian.status',
                'p_penelitian.sumber_data',
                'p_penelitian.bukti',
                'p_penelitian.created_at',
                'p_penelitian.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_penelitian.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_penelitian.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_penelitian.sumber_data', $sumber);
        }

        if ($tahun = request('filter_tahun')) {
            $query->where('p_penelitian.tahun', $tahun);
        }

        $penelitian = $query->orderBy('p_penelitian.id_penelitian')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Judul Penelitian');
        $sheet->setCellValue('D1', 'Skema');
        $sheet->setCellValue('E1', 'Tahun');
        $sheet->setCellValue('F1', 'Dana');
        $sheet->setCellValue('G1', 'Peran');
        $sheet->setCellValue('H1', 'Melibatkan Mahasiswa S2');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Sumber Data');
        $sheet->setCellValue('K1', 'Bukti');
        $sheet->setCellValue('L1', 'Created At');
        $sheet->setCellValue('M1', 'Updated At');

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($penelitian as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->judul_penelitian);
            $sheet->setCellValue('D' . $row, $data->skema);
            $sheet->setCellValue('E' . $row, $data->tahun);
            $sheet->setCellValue('F' . $row, $data->dana);
            $sheet->setCellValue('G' . $row, $data->peran);
            $sheet->setCellValue('H' . $row, $data->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak');
            $sheet->setCellValue('I' . $row, $data->status);
            $sheet->setCellValue('J' . $row, $data->sumber_data);
            // Tambahkan link ke file bukti
            if ($data->bukti) {
                $url = url('storage/portofolio/penelitian/' . $data->bukti);
                $sheet->setCellValue('K' . $row, 'Lihat File');
                $sheet->getCell('K' . $row)->getHyperlink()->setUrl($url);
            } else {
                $sheet->setCellValue('K' . $row, 'Tidak ada file');
            }
            $sheet->setCellValue('L' . $row, $data->created_at);
            $sheet->setCellValue('M' . $row, $data->updated_at);

            $row++;
            $no++;
        }

        foreach (range('A', 'M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data Penelitian");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penelitian ' . date("Y-m-d H-i-s") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $query = PPenelitianModel::with('user.profile')->orderBy('id_penelitian');

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('sumber_data', $sumber);
        }

        if ($tahun = request('filter_tahun')) {
            $query->where('tahun', $tahun);
        }

        $penelitian = $query->get();

        $data = $penelitian->map(function ($item) {
            return [
                'id_penelitian' => $item->id_penelitian,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'judul_penelitian' => $item->judul_penelitian,
                'skema' => $item->skema,
                'tahun' => $item->tahun,
                'dana' => 'Rp ' . number_format($item->dana, 0, ',', '.'),
                'peran' => ucfirst($item->peran),
                'melibatkan_mahasiswa_s2' => $item->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak',
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.penelitian.export_pdf', [
            'penelitian' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Penelitian ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
