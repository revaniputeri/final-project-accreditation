<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PPrestasiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PPrestasiDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PPrestasiController extends Controller
{
    public function index(PPrestasiDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('portofolio.prestasi.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('portofolio.prestasi.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'prestasi_yang_dicapai' => 'required|string|max:255',
                'waktu_pencapaian' => 'required|date',
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

                $exists = PPrestasiModel::where('id_user', $id_user)
                    ->where('prestasi_yang_dicapai', $request->input('prestasi_yang_dicapai'))
                    ->where('waktu_pencapaian', $request->input('waktu_pencapaian'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan prestasi dan waktu pencapaian yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'prestasi_yang_dicapai',
                    'waktu_pencapaian',
                    'tingkat',
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
                    $filename = $this->generateUniqueFilename('public/portofolio/prestasi', $filename);
                    $path = $file->storeAs('public/portofolio/prestasi', $filename);
                    $data['bukti'] = $filename;
                }

                PPrestasiModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data prestasi berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data prestasi',
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

        $prestasi = PPrestasiModel::findOrFail($id);
        return view('portofolio.prestasi.edit_ajax', compact('prestasi', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'prestasi_yang_dicapai' => 'required|string|max:255',
                'waktu_pencapaian' => 'required|date',
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

            $prestasi = PPrestasiModel::findOrFail($id);

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

                $exists = PPrestasiModel::where('id_user', $id_user)
                    ->where('prestasi_yang_dicapai', $request->input('prestasi_yang_dicapai'))
                    ->where('waktu_pencapaian', $request->input('waktu_pencapaian'))
                    ->where('id_prestasi', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan prestasi dan waktu pencapaian yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'prestasi_yang_dicapai',
                    'waktu_pencapaian',
                    'tingkat',
                ]);

                if ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                }

                if ($request->hasFile('bukti')) {
                    if ($prestasi->bukti && Storage::exists('public/portofolio/prestasi/' . $prestasi->bukti)) {
                        Storage::delete('public/portofolio/prestasi/' . $prestasi->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($prestasi->user && $prestasi->user->profile) {
                        $nidnPrefix = $prestasi->user->profile->nidn ? $prestasi->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/prestasi', $filename);
                    $path = $file->storeAs('public/portofolio/prestasi', $filename);
                    $data['bukti'] = $filename;
                }

                $prestasi->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data prestasi berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data prestasi',
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
        $prestasi = PPrestasiModel::findOrFail($id);
        return view('portofolio.prestasi.confirm_ajax', compact('prestasi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $prestasi = PPrestasiModel::findOrFail($id);

        try {
            if ($prestasi->bukti && Storage::exists('public/portofolio/prestasi/' . $prestasi->bukti)) {
                Storage::delete('public/portofolio/prestasi/' . $prestasi->bukti);
            }
            $prestasi->delete();

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
        $prestasi = PPrestasiModel::with('user.profile')->findOrFail($id);
        return view('portofolio.prestasi.detail_ajax', compact('prestasi'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $prestasi = PPrestasiModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $prestasi->status = $request->input('status');
            $prestasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.prestasi.validasi_ajax', compact('prestasi'));
    }

    public function import()
    {
        return view('portofolio.prestasi.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_prestasi' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_prestasi');
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
                $prestasiYangDicapai = trim($values['B']);
                $waktuPencapaian = trim($values['C']);

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $user = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$user) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                $isDuplicate = PPrestasiModel::where('id_user', $user->id_user)
                    ->where('prestasi_yang_dicapai', $prestasiYangDicapai)
                    ->where('waktu_pencapaian', $waktuPencapaian)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn, prestasi dan waktu pencapaian sudah ada.";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $user->id_user,
                    'prestasi_yang_dicapai' => $prestasiYangDicapai,
                    'waktu_pencapaian' => $waktuPencapaian,
                    'tingkat' => $values['D'],
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'prestasi_yang_dicapai' => 'required|string|max:255',
                    'waktu_pencapaian' => 'required|date',
                    'tingkat' => 'required|in:Lokal,Nasional,Internasional',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $user->id_user,
                    'prestasi_yang_dicapai' => $prestasiYangDicapai,
                    'waktu_pencapaian' => $waktuPencapaian,
                    'tingkat' => $values['D'],
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

            $insertedCount = PPrestasiModel::insert($insertData);

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
        $query = PPrestasiModel::join('user', 'p_prestasi.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_prestasi.prestasi_yang_dicapai',
                'p_prestasi.waktu_pencapaian',
                'p_prestasi.tingkat',
                'p_prestasi.status',
                'p_prestasi.sumber_data',
                'p_prestasi.bukti',
                'p_prestasi.created_at',
                'p_prestasi.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_prestasi.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_prestasi.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_prestasi.sumber_data', $sumber);
        }

        $prestasi = $query->orderBy('p_prestasi.id_prestasi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Prestasi Yang Dicapai');
        $sheet->setCellValue('D1', 'Waktu Pencapaian');
        $sheet->setCellValue('E1', 'Tingkat');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Sumber Data');
        $sheet->setCellValue('H1', 'Bukti');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($prestasi as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->prestasi_yang_dicapai);
            $sheet->setCellValue('D' . $row, date('d-m-Y', strtotime($data->waktu_pencapaian)));
            $sheet->setCellValue('E' . $row, $data->tingkat);
            $sheet->setCellValue('F' . $row, $data->status);
            $sheet->setCellValue('G' . $row, $data->sumber_data);
            if ($data->bukti) {
                $url = url('storage/portofolio/prestasi/' . $data->bukti);
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

        $sheet->setTitle("Data Prestasi");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Prestasi ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PPrestasiModel::with('user.profile')->orderBy('id_prestasi');

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

        $prestasi = $query->get();

        $data = $prestasi->map(function ($item) {
            return [
                'id_prestasi' => $item->id_prestasi,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'prestasi_yang_dicapai' => $item->prestasi_yang_dicapai,
                'waktu_pencapaian' => date('d-m-Y', strtotime($item->waktu_pencapaian)),
                'tingkat' => $item->tingkat,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.prestasi.export_pdf', [
            'prestasi' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Prestasi ' . date('d-m-Y H:i:s') . '.pdf');
    }

    public function validasi_update(Request $request, $id)
    {
        $prestasi = PPrestasiModel::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Tervalidasi,Tidak Valid',
        ]);

        $prestasi->status = $request->input('status');
        $prestasi->save();

        return response()->json([
            'status' => true,
            'message' => 'Status berhasil diperbarui',
        ]);
    }
}
