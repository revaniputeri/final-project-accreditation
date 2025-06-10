<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PHKIModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PHKIDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PHKIController extends Controller
{
    public function index(PHKIDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $role === 'ADM';
        $isDos = $role === 'DOS';
        $isAng = $role === 'ANG';

        // Distribusi jenis skema HKI (pie chart)
        $skemaDistribution = PHKIModel::select('skema', DB::raw('count(*) as total'))
            ->groupBy('skema')
            ->orderBy('total', 'desc')
            ->get();

        // Keterlibatan mahasiswa S2 (bar chart)
        $mahasiswaS2Distribution = PHKIModel::select('melibatkan_mahasiswa_s2', DB::raw('count(*) as total'))
            ->groupBy('melibatkan_mahasiswa_s2')
            ->orderBy('melibatkan_mahasiswa_s2')
            ->get();

        // Tren HKI per tahun (line chart)
        $trenPerTahun = PHKIModel::select('tahun', DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Prepare data arrays for charts
        $skemaLabels = $skemaDistribution->pluck('skema');
        $skemaData = $skemaDistribution->pluck('total');

        $mahasiswaS2Labels = $mahasiswaS2Distribution->map(function ($item) {
            return $item->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak';
        });
        $mahasiswaS2Data = $mahasiswaS2Distribution->pluck('total');

        $trenLabels = $trenPerTahun->pluck('tahun');
        $trenData = $trenPerTahun->pluck('total');

        return $dataTable->render('portofolio.hki.index', compact(
            'isAdm',
            'isAng',
            'isDos',
            'skemaLabels',
            'skemaData',
            'mahasiswaS2Labels',
            'mahasiswaS2Data',
            'trenLabels',
            'trenData'
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

        return view('portofolio.hki.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'judul' => 'required|string|max:255',
                'tahun' => 'required|integer',
                'skema' => 'required|string|max:100',
                'nomor' => 'required|string|max:255',
                'melibatkan_mahasiswa_s2' => 'nullable|boolean',
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
                        'message' => 'ID user tidak ditemukan.'
                    ]);
                }

                // Cek duplikat judul + nomor untuk user yang sama
                $exists = PHKIModel::where('id_user', $id_user)
                    ->where('nomor', $request->input('nomor'))
                    ->where('id_hki', '!=', $request->input('id_hki'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan Nomor HKI dan user yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul',
                    'tahun',
                    'skema',
                    'nomor',
                    'melibatkan_mahasiswa_s2'
                ]);

                $data['id_user'] = $id_user;

                if ($role === 'DOS') {
                    $data['status'] = 'Tervalidasi';
                    $data['sumber_data'] = 'dosen';
                } elseif ($role === 'ADM') {
                    $data['status'] = 'Perlu Validasi';
                    $data['sumber_data'] = 'p3m';
                } else {
                    $data['status'] = $request->input('status', 'Perlu Validasi');
                    $data['sumber_data'] = $request->input('sumber_data', 'p3m');
                }

                if ($request->hasFile('bukti')) {
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($user && $user->profile) {
                        $nidnPrefix = $user->profile->nidn ? $user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/hki', $filename);
                    $file->storeAs('public/portofolio/hki', $filename);
                    $data['bukti'] = $filename;
                }

                PHKIModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data HKI berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax (HKI): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data HKI',
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

        $user = Auth::user();
        $role = $user && $user->level ? $user->level->kode_level : null;

        $hki = PHKIModel::findOrFail($id);
        return view('portofolio.hki.edit_ajax', compact('hki', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        $user = Auth::user();
        $role = $user && $user->level ? $user->level->kode_level : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul' => 'required|string|max:255',
                'tahun' => 'required|integer',
                'skema' => 'required|string|max:100',
                'nomor' => 'required|string|max:255',
                'melibatkan_mahasiswa_s2' => 'nullable|boolean',
                'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
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

            $hki = PHKIModel::findOrFail($id);

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

                // Cek duplikat nomor + user, kecuali record yang sedang diupdate
                $exists = PHKIModel::where('id_user', $id_user)
                    ->where('nomor', $request->input('nomor'))
                    ->where('id_hki', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan Nomor dan user yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul',
                    'tahun',
                    'skema',
                    'nomor',
                    'melibatkan_mahasiswa_s2',
                    'bukti',
                ]);

                if ($role === 'DOS') {
                    $data['status'] = 'Tervalidasi';
                    $data['sumber_data'] = 'dosen';
                } elseif ($role === 'ADM') {
                    $data['status'] = 'Perlu Validasi';
                    $data['sumber_data'] = 'p3m';
                } else {
                    $data['status'] = $request->input('status', 'Perlu Validasi');
                    $data['sumber_data'] = $request->input('sumber_data', 'p3m');
                }

                if ($request->hasFile('bukti')) {
                    if ($hki->bukti && Storage::exists('public/portofolio/hki/' . $hki->bukti)) {
                        Storage::delete('public/portofolio/hki/' . $hki->bukti);
                    }

                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($hki->user && $hki->user->profile) {
                        $nidnPrefix = $hki->user->profile->nidn ? $hki->user->profile->nidn . '_' : '';
                    }

                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/hki', $filename);
                    $file->storeAs('public/portofolio/hki', $filename);
                    $data['bukti'] = $filename;
                }

                $hki->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data HKI berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax PHKI: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data HKI',
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
        $hki = PHKIModel::findOrFail($id);
        return view('portofolio.hki.confirm_ajax', compact('hki'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $hki = PHKIModel::findOrFail($id);

        try {
            if ($hki->bukti && Storage::exists('public/portofolio/hki/' . $hki->bukti)) {
                Storage::delete('public/portofolio/hki/' . $hki->bukti);
            }
            $hki->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data HKI berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in delete_ajax PHKI: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data HKI'
            ]);
        }
    }

    public function detail_ajax($id)
    {
        $hki = PHKIModel::with('user.profile')->findOrFail($id);
        return view('portofolio.hki.detail_ajax', compact('hki'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $hki = PHKIModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $hki->status = $request->input('status');
            $hki->save();

            return response()->json([
                'status' => true,
                'message' => 'Status HKI berhasil diperbarui',
            ]);
        }

        return view('portofolio.hki.validasi_ajax', compact('hki'));
    }

    public function import()
    {
        return view('portofolio.hki.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_hki' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_hki');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $insertData = [];
            $skippedData = [];
            $errors = [];

            $user = Auth::user();
            $role = $user && $user->level ? $user->level->kode_level : null;
            $userNidn = DB::table('profile_user')->where('id_user', $user->id_user)->value('nidn');

            foreach ($data as $row => $values) {
                if ($row == 1) continue; // Skip header

                $nidn = trim($values['A'] ?? '');
                $judul = trim($values['B'] ?? '');
                $tahun = $values['C'] ?? null;
                $skema = $values['D'] ?? null;
                $nomor = $values['E'] ?? null;
                $melibatkan_mahasiswa_s2 = strtolower(trim($values['F'] ?? ''));


                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data milik Anda (NIDN $userNidn).";
                    continue;
                }

                $profile = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$profile) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user.";
                    continue;
                }

                $isDuplicate = PHKIModel::where('id_user', $profile->id_user)
                    ->where('judul', $judul)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn dan Judul '$judul' sudah ada.";
                    continue;
                }
                if ($melibatkan_mahasiswa_s2 !== '' && !in_array($melibatkan_mahasiswa_s2, ['ya', 'tidak'])) {
                    $errors[] = "Baris $row: Nilai kolom 'Melibatkan Mahasiswa S2' harus 'ya', 'tidak', atau kosong.";
                    continue;
                }

                $isMelibatkan = ($melibatkan_mahasiswa_s2 === 'ya') ? 1 : 0;


                $validator = Validator::make([
                    'id_user' => $profile->id_user,
                    'judul' => $judul,
                    'tahun' => $tahun,
                    'skema' => $skema,
                    'nomor' => $nomor,
                    'melibatkan_mahasiswa_s2' => $melibatkan_mahasiswa_s2
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'judul' => 'required|string|max:255',
                    'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'skema' => 'nullable|string|max:255',
                    'nomor' => 'nullable|string|max:255',
                    'melibatkan_mahasiswa_s2' => 'nullable|in:ya,tidak',
                ]);


                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $melibatkan_mahasiswa_s2 = strtolower(trim($melibatkan_mahasiswa_s2));
                $isMelibatkan = ($melibatkan_mahasiswa_s2 === 'ya') ? 1 : 0;

                $insertData[] = [
                    'id_user' => $profile->id_user,
                    'judul' => $judul,
                    'tahun' => $tahun,
                    'skema' => $skema,
                    'nomor' => $nomor,
                    'melibatkan_mahasiswa_s2' => $isMelibatkan,
                    'status' => $role === 'DOS' ? 'Tervalidasi' : 'perlu validasi',
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
                    'message' => 'Tidak ada data baru yang valid untuk diimpor:' .
                        "\n" . implode("\n", array_slice($allMessages, 0, 1)) .
                        (count($allMessages) > 3 ? "\n...dan " . (count($allMessages) - 3) . " lainnya." : ''),
                    'showConfirmButton' => true
                ], 422);
            }

            PHKIModel::insert($insertData);

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Import data berhasil.',
                'inserted_count' => count($insertData),
                'skipped_count' => count($skippedData),
                'info' => $allMessages,
                'error_count' => count($errors)
            ]);
        } catch (\Exception $e) {
            Log::error('Import HKI error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file.',
                'error' => $e->getMessage(),
                'showConfirmButton' => true
            ], 500);
        }
    }

    public function export_excel()
    {
        $query = PHKIModel::join('user', 'p_hki.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_hki.judul',
                'p_hki.tahun',
                'p_hki.skema',
                'p_hki.nomor',
                'p_hki.melibatkan_mahasiswa_s2',
                'p_hki.status',
                'p_hki.sumber_data',
                'p_hki.bukti',
                'p_hki.created_at',
                'p_hki.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user && $user->level ? $user->level->kode_level : null;
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_hki.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_hki.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_hki.sumber_data', $sumber);
        }

        $hki = $query->orderBy('p_hki.id_hki')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Judul');
        $sheet->setCellValue('D1', 'Tahun');
        $sheet->setCellValue('E1', 'Skema');
        $sheet->setCellValue('F1', 'Nomor');
        $sheet->setCellValue('G1', 'Melibatkan Mahasiswa S2');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Sumber Data');
        $sheet->setCellValue('J1', 'Bukti');
        $sheet->setCellValue('K1', 'Created At');
        $sheet->setCellValue('L1', 'Updated At');

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($hki as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->judul);
            $sheet->setCellValue('D' . $row, $data->tahun);
            $sheet->setCellValue('E' . $row, $data->skema);
            $sheet->setCellValue('F' . $row, $data->nomor);
            $sheet->setCellValue('G' . $row, $data->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak');
            $sheet->setCellValue('H' . $row, $data->status);
            $sheet->setCellValue('I' . $row, $data->sumber_data);

            if ($data->bukti) {
                $url = url('storage/portofolio/hki/' . $data->bukti);
                $sheet->setCellValue('J' . $row, 'Lihat File');
                $sheet->getCell('J' . $row)->getHyperlink()->setUrl($url);
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                $sheet->getStyle('J' . $row)->getFont()->setUnderline(true);
            } else {
                $sheet->setCellValue('J' . $row, 'Tidak ada file');
            }

            $sheet->setCellValue('K' . $row, $data->created_at);
            $sheet->setCellValue('L' . $row, $data->updated_at);

            $row++;
            $no++;
        }

        // Set auto size kolom
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data HKI");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data HKI ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PHKIModel::with(['dosen.profile'])->orderBy('id_hki');

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

        $hki = $query->get();

        $data = $hki->map(function ($item) {
            return [
                // 'id_hki' => $item->id_hki,
                'nama_dosen' => optional(optional($item->dosen)->profile)->nama_lengkap ?? '-',
                'judul' => $item->judul,
                'tahun' => $item->tahun,
                'skema' => $item->skema,
                'nomor' => $item->nomor,
                'melibatkan_mahasiswa_s2' => $item->melibatkan_mahasiswa_s2,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.hki.export_pdf', [
            'hki' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data_HKI_' . date('d-m-Y_H-i-s') . '.pdf');
    }
}
