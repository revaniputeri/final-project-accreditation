<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\POrganisasiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\POrganisasiDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class POrganisasiController extends Controller
{
    public function index(POrganisasiDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('portofolio.organisasi.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('portofolio.organisasi.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'nama_organisasi' => 'required|string|max:255',
                'kurun_waktu' => 'required|string|max:100',
                'tingkat' => 'required|in:Lokal,Nasional,Internasional',
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

                // Custom duplicate check for id_user and nama organisasi
                $exists = POrganisasiModel::where('id_user', $id_user)
                    ->where('nama_organisasi', $request->input('nama_organisasi'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Nama Organisasi yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'nama_organisasi',
                    'kurun_waktu',
                    'tingkat',
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
                    $filename = $this->generateUniqueFilename('public/portofolio/organisasi', $filename);
                    $path = $file->storeAs('public/portofolio/organisasi', $filename);
                    $data['bukti'] = $filename;
                }

                POrganisasiModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data organisasi berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data organisasi',
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

        $organisasi = POrganisasiModel::findOrFail($id);
        return view('portofolio.organisasi.edit_ajax', compact('organisasi', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_organisasi' => 'required|string|max:255',
                'kurun_waktu' => 'required|string|max:100',
                'tingkat' => 'required|in:Lokal,Nasional,Internasional',
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

            $organisasi = POrganisasiModel::findOrFail($id);

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

                // Custom duplicate check for id_user and nama organisasi excluding current record
                $exists = POrganisasiModel::where('id_user', $id_user)
                    ->where('nama_organisasi', $request->input('nama_organisasi'))
                    ->where('id_organisasi', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Nama Organisasi yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'nama_organisasi',
                    'kurun_waktu',
                    'tingkat',
                ]);

                if ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                }

                if ($request->hasFile('bukti')) {
                    if ($organisasi->bukti && Storage::exists('public/portofolio/organisasi/' . $organisasi->bukti)) {
                        Storage::delete('public/portofolio/organisasi/' . $organisasi->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($organisasi->user && $organisasi->user->profile) {
                        $nidnPrefix = $organisasi->user->profile->nidn ? $organisasi->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/organisasi', $filename);
                    $path = $file->storeAs('public/portofolio/organisasi', $filename);
                    $data['bukti'] = $filename;
                }

                $organisasi->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data organisasi berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data organisasi',
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
        $organisasi = POrganisasiModel::findOrFail($id);
        return view('portofolio.organisasi.confirm_ajax', compact('organisasi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $organisasi = POrganisasiModel::findOrFail($id);

        try {
            if ($organisasi->bukti && Storage::exists('public/portofolio/organisasi/' . $organisasi->bukti)) {
                Storage::delete('public/portofolio/organisasi/' . $organisasi->bukti);
            }
            $organisasi->delete();

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
        $organisasi = POrganisasiModel::with('user.profile')->findOrFail($id);
        return view('portofolio.organisasi.detail_ajax', compact('organisasi'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $organisasi = POrganisasiModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $organisasi->status = $request->input('status');
            $organisasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.organisasi.validasi_ajax', compact('organisasi'));
    }

    public function import()
    {
        return view('portofolio.organisasi.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_organisasi' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_organisasi');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $insertData = [];
            $skippedData = [];
            $errors = [];

            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;
            $userNidn = null;

            if ($role === 'DOS') {
                $userNidn = DB::table('profile_user')->where('id_user', $user->id_user)->value('nidn');
            }

            foreach ($data as $row => $values) {
                if ($row == 1) continue; // Skip header row

                $nidn = trim($values['A'] ?? '');
                $namaOrganisasi = trim($values['B'] ?? '');
                $kurunWaktu = trim($values['C'] ?? '');
                $tingkat = trim($values['D'] ?? '');

                // Validasi data kosong
                if (empty($nidn) || empty($namaOrganisasi) || empty($kurunWaktu) || empty($tingkat)) {
                    $errors[] = "Baris $row: Data tidak lengkap. Pastikan semua kolom terisi.";
                    continue;
                }

                // Cek jika DOS hanya bisa import data dengan NIDN sendiri
                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                // Cari user berdasarkan NIDN
                $profileUser = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$profileUser) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                // Cek duplikat kombinasi id_user dan nama organisasi
                $isDuplicate = POrganisasiModel::where('id_user', $profileUser->id_user)
                    ->where('nama_organisasi', $namaOrganisasi)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn dan Nama Organisasi '$namaOrganisasi' sudah ada.";
                    continue;
                }

                // Validasi data
                $validator = Validator::make([
                    'id_user' => $profileUser->id_user,
                    'nama_organisasi' => $namaOrganisasi,
                    'kurun_waktu' => $kurunWaktu,
                    'tingkat' => $tingkat,
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'nama_organisasi' => 'required|string|max:255',
                    'kurun_waktu' => 'required|string|max:100',
                    'tingkat' => 'required|in:Lokal,Nasional,Internasional',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $profileUser->id_user,
                    'nama_organisasi' => $namaOrganisasi,
                    'kurun_waktu' => $kurunWaktu,
                    'tingkat' => $tingkat,
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
                    'message' => 'Tidak ada data baru yang valid untuk diimport.' .
                        (count($allMessages) > 0 ? "\n\nDetail:\n" . implode("\n", array_slice($allMessages, 0, 5)) .
                        (count($allMessages) > 5 ? "\n...dan " . (count($allMessages) - 5) . " error lainnya." : '') : ''),
                    'showConfirmButton' => true
                ], 422);
            }

            $insertedCount = count($insertData);
            POrganisasiModel::insert($insertData);

            $message = "Import data berhasil! $insertedCount data organisasi berhasil ditambahkan.";
            if (count($skippedData) > 0) {
                $message .= "\n" . count($skippedData) . " data dilewati karena sudah ada.";
            }
            if (count($errors) > 0) {
                $message .= "\n" . count($errors) . " data gagal diimport karena error validasi.";
            }

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => $message,
                'inserted_count' => $insertedCount,
                'skipped_count' => count($skippedData),
                'error_count' => count($errors),
                'details' => count($allMessages) > 0 ? array_slice($allMessages, 0, 10) : []
            ]);

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'alert' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage(),
                'showConfirmButton' => true
            ], 500);
        }
    }

    public function export_excel()
    {
        $query = POrganisasiModel::join('user', 'p_organisasi.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_organisasi.nama_organisasi',
                'p_organisasi.kurun_waktu',
                'p_organisasi.tingkat',
                'p_organisasi.status',
                'p_organisasi.sumber_data',
                'p_organisasi.bukti',
                'p_organisasi.created_at',
                'p_organisasi.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_organisasi.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_organisasi.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_organisasi.sumber_data', $sumber);
        }

        $organisasi = $query->orderBy('p_organisasi.id_organisasi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Nama Organisasi');
        $sheet->setCellValue('D1', 'Kurun Waktu');
        $sheet->setCellValue('E1', 'Tingkat');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Sumber Data');
        $sheet->setCellValue('H1', 'Bukti');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($organisasi as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->nama_organisasi);
            $sheet->setCellValue('D' . $row, $data->kurun_waktu);
            $sheet->setCellValue('E' . $row, $data->tingkat);
            $sheet->setCellValue('F' . $row, $data->status);
            $sheet->setCellValue('G' . $row, $data->sumber_data);
            if ($data->bukti) {
                $url = url('storage/portofolio/organisasi/' . $data->bukti);
                $sheet->setCellValue('H' . $row, 'Lihat File');
                $sheet->getCell('H' . $row)->getHyperlink()->setUrl($url);
            } else {
                $sheet->setCellValue('H' . $row, 'Tidak ada file');
            }
            $sheet->setCellValue('I' . $row, $data->created_at);
            $sheet->setCellValue('J' . $row, $data->updated_at);

            $row++;
            $no++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data Organisasi");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Organisasi ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = POrganisasiModel::with('user.profile')->orderBy('id_organisasi');

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

        $organisasi = $query->get();

        $data = $organisasi->map(function ($item) {
            return [
                'id_organisasi' => $item->id_organisasi,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'nama_organisasi' => $item->nama_organisasi,
                'kurun_waktu' => $item->kurun_waktu,
                'tingkat' => $item->tingkat,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.organisasi.export_pdf', [
            'organisasi' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Organisasi ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
