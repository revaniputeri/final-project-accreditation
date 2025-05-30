<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PKegiatanModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PKegiatanDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PKegiatanController extends Controller
{
    public function index(PKegiatanDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('p_kegiatan.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('p_kegiatan.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'jenis_kegiatan' => 'required|string|max:100',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'tempat' => 'required|string|max:255',
                'peran' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
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

                $data = $request->only([
                    'jenis_kegiatan',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'tempat',
                    'peran',
                    'deskripsi',
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
                    $filename = $this->generateUniqueFilename('public/p_kegiatan', $filename);
                    $path = $file->storeAs('public/p_kegiatan', $filename);
                    $data['bukti'] = $filename;
                }

                PKegiatanModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data kegiatan berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data kegiatan',
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

        $kegiatan = PKegiatanModel::findOrFail($id);
        return view('p_kegiatan.edit_ajax', compact('kegiatan', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'jenis_kegiatan' => 'required|string|max:100',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'tempat' => 'required|string|max:255',
                'peran' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
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

            $kegiatan = PKegiatanModel::findOrFail($id);

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

                $data = $request->only([
                    'jenis_kegiatan',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'tempat',
                    'peran',
                    'deskripsi',
                ]);

                if ($role === 'ADM') {
                    $data['status'] = 'perlu validasi';
                }

                if ($request->hasFile('bukti')) {
                    if ($kegiatan->bukti && Storage::exists('public/p_kegiatan/' . $kegiatan->bukti)) {
                        Storage::delete('public/p_kegiatan/' . $kegiatan->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($kegiatan->user && $kegiatan->user->profile) {
                        $nidnPrefix = $kegiatan->user->profile->nidn ? $kegiatan->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/p_kegiatan', $filename);
                    $path = $file->storeAs('public/p_kegiatan', $filename);
                    $data['bukti'] = $filename;
                }

                $kegiatan->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data kegiatan berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data kegiatan',
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
        $kegiatan = PKegiatanModel::findOrFail($id);
        return view('p_kegiatan.confirm_ajax', compact('kegiatan'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $kegiatan = PKegiatanModel::findOrFail($id);

        try {
            if ($kegiatan->bukti && Storage::exists('public/p_kegiatan/' . $kegiatan->bukti)) {
                Storage::delete('public/p_kegiatan/' . $kegiatan->bukti);
            }
            $kegiatan->delete();

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
        $kegiatan = PKegiatanModel::with('user.profile')->findOrFail($id);
        return view('p_kegiatan.detail_ajax', compact('kegiatan'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $kegiatan = PKegiatanModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:Tervalidasi,Tidak Valid',
            ]);

            $kegiatan->status = $request->input('status');
            $kegiatan->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('p_kegiatan.validasi_ajax', compact('kegiatan'));
    }

    public function import()
    {
        return view('p_kegiatan.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_kegiatan' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_kegiatan');
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

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $user = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$user) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $user->id_user,
                    'jenis_kegiatan' => $values['B'],
                    'tanggal_mulai' => $values['C'],
                    'tanggal_selesai' => $values['D'],
                    'tempat' => $values['E'],
                    'peran' => $values['F'],
                    'deskripsi' => $values['G'],
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'jenis_kegiatan' => 'required|string|max:100',
                    'tanggal_mulai' => 'required|date',
                    'tanggal_selesai' => 'required|date',
                    'tempat' => 'required|string|max:255',
                    'peran' => 'required|string|max:100',
                    'deskripsi' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $user->id_user,
                    'jenis_kegiatan' => $values['B'],
                    'tanggal_mulai' => $values['C'],
                    'tanggal_selesai' => $values['D'],
                    'tempat' => $values['E'],
                    'peran' => $values['F'],
                    'deskripsi' => $values['G'] ?? null,
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

            $insertedCount = PKegiatanModel::insert($insertData);

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
        $query = PKegiatanModel::join('user', 'p_kegiatan.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'p_kegiatan.id_kegiatan',
                'profile_user.nama_lengkap as nama_user',
                'p_kegiatan.jenis_kegiatan',
                'p_kegiatan.tempat',
                'p_kegiatan.waktu',
                'p_kegiatan.peran',
                'p_kegiatan.status',
                'p_kegiatan.sumber_data',
                'p_kegiatan.bukti',
                'p_kegiatan.created_at',
                'p_kegiatan.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_kegiatan.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_kegiatan.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_kegiatan.sumber_data', $sumber);
        }

        $kegiatan = $query->orderBy('p_kegiatan.id_kegiatan')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Kegiatan');
        $sheet->setCellValue('C1', 'Nama Dosen');
        $sheet->setCellValue('D1', 'Jenis Kegiatan');
        $sheet->setCellValue('E1', 'Tempat');
        $sheet->setCellValue('F1', 'Waktu');
        $sheet->setCellValue('G1', 'Peran');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Sumber Data');
        $sheet->setCellValue('J1', 'Bukti');
        $sheet->setCellValue('K1', 'Created At');
        $sheet->setCellValue('L1', 'Updated At');

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($kegiatan as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->id_kegiatan);
            $sheet->setCellValue('C' . $row, $data->nama_user);
            $sheet->setCellValue('D' . $row, $data->jenis_kegiatan);
            $sheet->setCellValue('E' . $row, $data->tempat);
            $sheet->setCellValue('F' . $row, $data->waktu);
            $sheet->setCellValue('G' . $row, $data->peran);
            $sheet->setCellValue('H' . $row, $data->status);
            $sheet->setCellValue('I' . $row, $data->sumber_data);
            if ($data->bukti) {
                $url = url('storage/p_kegiatan/' . $data->bukti);
                $sheet->setCellValue('J' . $row, 'Lihat File');
                $sheet->getCell('J' . $row)->getHyperlink()->setUrl($url);
            } else {
                $sheet->setCellValue('J' . $row, 'Tidak ada file');
            }
            $sheet->setCellValue('K' . $row, $data->created_at);
            $sheet->setCellValue('L' . $row, $data->updated_at);

            $row++;
            $no++;
        }

        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle("Data Kegiatan");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Kegiatan ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PKegiatanModel::with('user.profile')->orderBy('id_kegiatan');

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

        $kegiatan = $query->get();

        $data = $kegiatan->map(function ($item) {
            return [
                'id_kegiatan' => $item->id_kegiatan,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'jenis_kegiatan' => $item->jenis_kegiatan,
                'tempat' => $item->tempat,
                'waktu' => $item->waktu,
                'peran' => $item->peran,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('p_kegiatan.export_pdf', [
            'kegiatan' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Kegiatan ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
