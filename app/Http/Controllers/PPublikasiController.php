<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PPublikasiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PPublikasiDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PPublikasiController extends Controller
{
    public function index(PPublikasiDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('p_publikasi.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('p_publikasi.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'judul' => 'required|string|max:255',
                'tempat_publikasi' => 'required|string|max:100',
                'tahun_publikasi' => 'required|integer',
                'jenis_publikasi' => 'required|in:jurnal,prosiding,poster',
                'dana' => 'required|numeric',
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

                $exists = PPublikasiModel::where('id_user', $id_user)
                    ->where('judul', $request->input('judul'))
                    ->where('tahun_publikasi', $request->input('tahun_publikasi'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan Judul dan Tahun Publikasi yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul',
                    'tempat_publikasi',
                    'tahun_publikasi',
                    'jenis_publikasi',
                    'dana',
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
                    $filename = $this->generateUniqueFilename('public/p_publikasi', $filename);
                    $path = $file->storeAs('public/p_publikasi', $filename);
                    $data['bukti'] = $filename;
                }

                PPublikasiModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data publikasi berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data publikasi',
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

        $publikasi = PPublikasiModel::findOrFail($id);
        return view('p_publikasi.edit_ajax', compact('publikasi', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul' => 'required|string|max:255',
                'tempat_publikasi' => 'required|string|max:100',
                'tahun_publikasi' => 'required|integer',
                'jenis_publikasi' => 'required|in:jurnal,prosiding,poster',
                'dana' => 'required|numeric',
                'melibatkan_mahasiswa_s2' => 'required|boolean',
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

            $publikasi = PPublikasiModel::findOrFail($id);

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

                // Duplicate check excluding current record
                $exists = PPublikasiModel::where('id_user', $id_user)
                    ->where('judul', $request->input('judul'))
                    ->where('tahun_publikasi', $request->input('tahun_publikasi'))
                    ->where('id_publikasi', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan Judul dan Tahun Publikasi yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul',
                    'tempat_publikasi',
                    'tahun_publikasi',
                    'jenis_publikasi',
                    'dana',
                    'melibatkan_mahasiswa_s2',
                    'bukti',
                ]);

                if ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                }

                if ($request->hasFile('bukti')) {
                    if ($publikasi->bukti && Storage::exists('public/p_publikasi/' . $publikasi->bukti)) {
                        Storage::delete('public/p_publikasi/' . $publikasi->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($publikasi->user && $publikasi->user->profile) {
                        $nidnPrefix = $publikasi->user->profile->nidn ? $publikasi->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/p_publikasi', $filename);
                    $path = $file->storeAs('public/p_publikasi', $filename);
                    $data['bukti'] = $filename;
                }

                $publikasi->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data publikasi berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data publikasi',
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
        $publikasi = PPublikasiModel::findOrFail($id);
        return view('p_publikasi.confirm_ajax', compact('publikasi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $publikasi = PPublikasiModel::findOrFail($id);

        try {
            if ($publikasi->bukti && Storage::exists('public/p_publikasi/' . $publikasi->bukti)) {
                Storage::delete('public/p_publikasi/' . $publikasi->bukti);
            }
            $publikasi->delete();

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
        $publikasi = PPublikasiModel::with('user.profile')->findOrFail($id);
        return view('p_publikasi.detail_ajax', compact('publikasi'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $publikasi = PPublikasiModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $publikasi->status = $request->input('status');
            $publikasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('p_publikasi.validasi_ajax', compact('publikasi'));
    }

    public function import()
    {
        return view('p_publikasi.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_publikasi' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_publikasi');
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
                $judul = trim($values['B']);
                $tempatPublikasi = trim($values['C']);
                $tahunPublikasi = trim($values['D']);
                $jenisPublikasi = strtolower(trim($values['E']));
                $dana = trim($values['F']);
                $melibatkanMhsS2 = strtolower(trim($values['G']));

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $profileUser = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$profileUser) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                $isDuplicate = PPublikasiModel::where('id_user', $profileUser->id_user)
                    ->where('judul', $judul)
                    ->where('tahun_publikasi', $tahunPublikasi)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn, Judul $judul dan Tahun Publikasi $tahunPublikasi sudah ada.";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $profileUser->id_user,
                    'judul' => $judul,
                    'tempat_publikasi' => $tempatPublikasi,
                    'tahun_publikasi' => $tahunPublikasi,
                    'jenis_publikasi' => $jenisPublikasi,
                    'dana' => $dana,
                    'melibatkan_mahasiswa_s2' => in_array($melibatkanMhsS2, ['true', '1', 'yes']) ? true : false,
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'judul' => 'required|string|max:255',
                    'tempat_publikasi' => 'required|string|max:100',
                    'tahun_publikasi' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'jenis_publikasi' => 'required|in:jurnal,prosiding,poster',
                    'dana' => 'required|numeric',
                    'melibatkan_mahasiswa_s2' => 'required|boolean',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $profileUser->id_user,
                    'judul' => $judul,
                    'tempat_publikasi' => $tempatPublikasi,
                    'tahun_publikasi' => $tahunPublikasi,
                    'jenis_publikasi' => $jenisPublikasi,
                    'dana' => $dana,
                    'melibatkan_mahasiswa_s2' => in_array($melibatkanMhsS2, ['true', '1', 'yes']) ? true : false,
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

            $insertedCount = PPublikasiModel::insert($insertData);

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
        $query = PPublikasiModel::join('user', 'p_publikasi.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'p_publikasi.id_publikasi',
                'profile_user.nama_lengkap as nama_user',
                'p_publikasi.judul',
                'p_publikasi.tempat_publikasi',
                'p_publikasi.tahun_publikasi',
                'p_publikasi.jenis_publikasi',
                'p_publikasi.dana',
                'p_publikasi.status',
                'p_publikasi.sumber_data',
                'p_publikasi.bukti',
                'p_publikasi.created_at',
                'p_publikasi.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_publikasi.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_publikasi.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_publikasi.sumber_data', $sumber);
        }

        $publikasi = $query->orderBy('p_publikasi.id_publikasi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Publikasi');
        $sheet->setCellValue('C1', 'Nama Dosen');
        $sheet->setCellValue('D1', 'Judul');
        $sheet->setCellValue('E1', 'Tempat Publikasi');
        $sheet->setCellValue('F1', 'Tahun Publikasi');
        $sheet->setCellValue('G1', 'Jenis Publikasi');
        $sheet->setCellValue('H1', 'Dana');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Sumber Data');
        $sheet->setCellValue('K1', 'Bukti');
        $sheet->setCellValue('L1', 'Created At');
        $sheet->setCellValue('M1', 'Updated At');

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($publikasi as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->id_publikasi);
            $sheet->setCellValue('C' . $row, $data->nama_user);
            $sheet->setCellValue('D' . $row, $data->judul);
            $sheet->setCellValue('E' . $row, $data->tempat_publikasi);
            $sheet->setCellValue('F' . $row, $data->tahun_publikasi);
            $sheet->setCellValue('G' . $row, ucfirst($data->jenis_publikasi));
            $sheet->setCellValue('H' . $row, $data->dana);
            $sheet->setCellValue('I' . $row, $data->status);
            $sheet->setCellValue('J' . $row, $data->sumber_data);
            if ($data->bukti) {
                $url = url('storage/p_publikasi/' . $data->bukti);
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

        $sheet->setTitle("Data Publikasi");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Publikasi ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PPublikasiModel::with('user.profile')->orderBy('id_publikasi');

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

        $publikasi = $query->get();

        $data = $publikasi->map(function ($item) {
            return [
                'id_publikasi' => $item->id_publikasi,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'judul' => $item->judul,
                'tempat_publikasi' => $item->tempat_publikasi,
                'tahun_publikasi' => $item->tahun_publikasi,
                'jenis_publikasi' => ucfirst($item->jenis_publikasi),
                'dana' => $item->dana,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('p_publikasi.export_pdf', [
            'publikasi' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Publikasi ' . date('d-m-Y H:i:s') . '.pdf');
    }
}

