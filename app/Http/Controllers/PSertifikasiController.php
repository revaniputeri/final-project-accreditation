<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PSertifikasiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PSertifikasiDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PSertifikasiController extends Controller
{
    public function index(PSertifikasiDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('portofolio.sertifikasi.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('portofolio.sertifikasi.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'tahun_diperoleh' => 'required|integer',
                'penerbit' => 'required|string|max:255',
                'nama_sertifikasi' => 'required|string|max:255',
                'nomor_sertifikat' => 'required|string|max:255',
                'masa_berlaku' => 'required|string|max:50',
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

                // Custom duplicate check for id_user and nomor_sertifikat
                $exists = PSertifikasiModel::where('id_user', $id_user)
                    ->where('nomor_sertifikat', $request->input('nomor_sertifikat'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Nomor Sertifikat yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'tahun_diperoleh',
                    'penerbit',
                    'nama_sertifikasi',
                    'nomor_sertifikat',
                    'masa_berlaku',
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
                    if ($role === 'ADM' && isset($nidn)) {
                        $nidnPrefix = $nidn . '_';
                    } elseif ($role === 'DOS') {
                        $profileUser = DB::table('profile_user')->where('id_user', $user->id_user)->first();
                        $nidnPrefix = $profileUser && $profileUser->nidn ? $profileUser->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/sertifikasi', $filename);
                    $path = $file->storeAs('public/portofolio/sertifikasi', $filename);
                    $data['bukti'] = $filename;
                }

                PSertifikasiModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data sertifikasi berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data sertifikasi',
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

        $sertifikasi = PSertifikasiModel::findOrFail($id);
        return view('portofolio.sertifikasi.edit_ajax', compact('sertifikasi', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'tahun_diperoleh' => 'required|integer',
                'penerbit' => 'required|string|max:255',
                'nama_sertifikasi' => 'required|string|max:255',
                'nomor_sertifikat' => 'required|string|max:255',
                'masa_berlaku' => 'required|string|max:50',
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

            $sertifikasi = PSertifikasiModel::findOrFail($id);

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

                $exists = PSertifikasiModel::where('id_user', $id_user)
                    ->where('nomor_sertifikat', $request->input('nomor_sertifikat'))
                    ->where('id_sertifikasi', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Nomor Sertifikat yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'tahun_diperoleh',
                    'penerbit',
                    'nama_sertifikasi',
                    'nomor_sertifikat',
                    'masa_berlaku',
                    'bukti',
                ]);

                if ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                }

                if ($request->hasFile('bukti')) {
                    if ($sertifikasi->bukti && Storage::exists('public/portofolio/sertifikasi/' . $sertifikasi->bukti)) {
                        Storage::delete('public/portofolio/sertifikasi/' . $sertifikasi->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($sertifikasi->user && $sertifikasi->user->profile) {
                        $nidnPrefix = $sertifikasi->user->profile->nidn ? $sertifikasi->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/sertifikasi', $filename);
                    $path = $file->storeAs('public/portofolio/sertifikasi', $filename);
                    $data['bukti'] = $filename;
                }

                $sertifikasi->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data sertifikasi berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data sertifikasi',
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
        $sertifikasi = PSertifikasiModel::findOrFail($id);
        return view('portofolio.sertifikasi.confirm_ajax', compact('sertifikasi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $sertifikasi = PSertifikasiModel::findOrFail($id);

        try {
            if ($sertifikasi->bukti && Storage::exists('public/portofolio/sertifikasi/' . $sertifikasi->bukti)) {
                Storage::delete('public/portofolio/sertifikasi/' . $sertifikasi->bukti);
            }
            $sertifikasi->delete();

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
        $sertifikasi = PSertifikasiModel::with('user.profile')->findOrFail($id);
        return view('portofolio.sertifikasi.detail_ajax', compact('sertifikasi'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $sertifikasi = PSertifikasiModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $sertifikasi->status = $request->input('status');
            $sertifikasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.sertifikasi.validasi_ajax', compact('sertifikasi'));
    }

    public function import()
    {
        return view('portofolio.sertifikasi.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_sertifikasi' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_sertifikasi');
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

                $nidn = trim($values['A']); // pastikan tidak ada ' di awal
                $nomorSertifikat = trim($values['E']);

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $user = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$user) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                // Cek duplikat kombinasi id_user dan nomor_sertifikat
                $isDuplicate = PSertifikasiModel::where('id_user', $user->id_user)
                    ->where('nomor_sertifikat', $nomorSertifikat)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn dan Nomor Sertifikat $nomorSertifikat sudah ada.";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $user->id_user,
                    'tahun_diperoleh' => $values['B'],
                    'penerbit' => $values['C'],
                    'nama_sertifikasi' => $values['D'],
                    'nomor_sertifikat' => $nomorSertifikat,
                    'masa_berlaku' => $values['F'],
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'tahun_diperoleh' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'penerbit' => 'required|string|max:255',
                    'nama_sertifikasi' => 'required|string|max:255',
                    'nomor_sertifikat' => 'required|string|max:255',
                    'masa_berlaku' => 'required|string|max:50',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $user->id_user,
                    'tahun_diperoleh' => $values['B'],
                    'penerbit' => $values['C'],
                    'nama_sertifikasi' => $values['D'],
                    'nomor_sertifikat' => $nomorSertifikat,
                    'masa_berlaku' => $values['F'],
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
                    'message' => 'Tidak ada data baru yang valid untuk diimport:' .
                        "\n" . implode("\n", array_slice($allMessages, 0, 1)) .
                        (count($allMessages) > 3 ? "\n...dan " . (count($allMessages) - 3) . " lainnya." : ''),
                    'showConfirmButton' => true
                ], 422);
            }

            $insertedCount = PSertifikasiModel::insert($insertData);

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
        $query = PSertifikasiModel::join('user', 'p_sertifikasi.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'p_sertifikasi.id_sertifikasi',
                'profile_user.nama_lengkap as nama_user',
                'p_sertifikasi.tahun_diperoleh',
                'p_sertifikasi.penerbit',
                'p_sertifikasi.nama_sertifikasi',
                'p_sertifikasi.nomor_sertifikat',
                'p_sertifikasi.masa_berlaku',
                'p_sertifikasi.status',
                'p_sertifikasi.sumber_data',
                'p_sertifikasi.bukti', // tambahkan kolom bukti
                'p_sertifikasi.created_at',
                'p_sertifikasi.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_sertifikasi.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_sertifikasi.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_sertifikasi.sumber_data', $sumber);
        }

        $sertifikasi = $query->orderBy('p_sertifikasi.id_sertifikasi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Sertifikasi');
        $sheet->setCellValue('C1', 'Nama Dosen');
        $sheet->setCellValue('D1', 'Tahun Diperoleh');
        $sheet->setCellValue('E1', 'Penerbit');
        $sheet->setCellValue('F1', 'Nama Sertifikasi');
        $sheet->setCellValue('G1', 'Nomor Sertifikat');
        $sheet->setCellValue('H1', 'Masa Berlaku');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Sumber Data');
        $sheet->setCellValue('K1', 'Bukti');
        $sheet->setCellValue('L1', 'Created At');
        $sheet->setCellValue('M1', 'Updated At');

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($sertifikasi as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->id_sertifikasi);
            $sheet->setCellValue('C' . $row, $data->nama_user);
            $sheet->setCellValue('D' . $row, $data->tahun_diperoleh);
            $sheet->setCellValue('E' . $row, $data->penerbit);
            $sheet->setCellValue('F' . $row, $data->nama_sertifikasi);
            $sheet->setCellValue('G' . $row, $data->nomor_sertifikat);
            $sheet->setCellValue('H' . $row, $data->masa_berlaku);
            $sheet->setCellValue('I' . $row, $data->status);
            $sheet->setCellValue('J' . $row, $data->sumber_data);
            // Tambahkan link ke file bukti
            if ($data->bukti) {
                $url = url('storage/portofolio/sertifikasi/' . $data->bukti);
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

        $sheet->setTitle("Data Sertifikasi");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Sertifikasi ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PSertifikasiModel::with('user.profile')->orderBy('id_sertifikasi');

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

        $sertifikasi = $query->get();

        $data = $sertifikasi->map(function ($item) {
            return [
                'id_sertifikasi' => $item->id_sertifikasi,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'tahun_diperoleh' => $item->tahun_diperoleh,
                'penerbit' => $item->penerbit,
                'nama_sertifikasi' => $item->nama_sertifikasi,
                'nomor_sertifikat' => $item->nomor_sertifikat,
                'masa_berlaku' => $item->masa_berlaku,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.sertifikasi.export_pdf', [
            'sertifikasi' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Sertifikasi ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
