<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PKaryaBukuModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PKaryaBukuDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PKaryaBukuController extends Controller
{
    public function index(PKaryaBukuDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        return $dataTable->render('p_karya_buku.index', compact('isAdm', 'isAng', 'isDos'));
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

        return view('p_karya_buku.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'judul_buku' => 'required|string|max:255',
                'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'penerbit' => 'required|string|max:100',
                'isbn' => 'required|string|max:50',
                'jumlah_halaman' => 'required|integer|min:1',
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

                // Custom duplicate check for id_user and isbn
                $exists = PKaryaBukuModel::where('id_user', $id_user)
                    ->where('isbn', $request->input('isbn'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan ISBN yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul_buku',
                    'tahun',
                    'penerbit',
                    'isbn',
                    'jumlah_halaman',
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
                    $filename = $this->generateUniqueFilename('public/p_karya_buku', $filename);
                    $path = $file->storeAs('public/p_karya_buku', $filename);
                    $data['bukti'] = $filename;
                }

                PKaryaBukuModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data karya buku berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data karya buku',
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

        $karyaBuku = PKaryaBukuModel::findOrFail($id);
        return view('p_karya_buku.edit_ajax', compact('karyaBuku', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->getRole() : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul_buku' => 'required|string|max:255',
                'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'penerbit' => 'required|string|max:100',
                'isbn' => 'required|string|max:50',
                'jumlah_halaman' => 'required|integer|min:1',
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

            $karyaBuku = PKaryaBukuModel::findOrFail($id);

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

                $exists = PKaryaBukuModel::where('id_user', $id_user)
                    ->where('isbn', $request->input('isbn'))
                    ->where('id_karya_buku', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan ISBN yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul_buku',
                    'tahun',
                    'penerbit',
                    'isbn',
                    'jumlah_halaman',
                ]);

                if ($request->hasFile('bukti')) {
                    if ($karyaBuku->bukti && Storage::exists('public/p_karya_buku/' . $karyaBuku->bukti)) {
                        Storage::delete('public/p_karya_buku/' . $karyaBuku->bukti);
                    }
                    $file = $request->file('bukti');
                    $nidnPrefix = '';
                    if ($karyaBuku->user && $karyaBuku->user->profile) {
                        $nidnPrefix = $karyaBuku->user->profile->nidn ? $karyaBuku->user->profile->nidn . '_' : '';
                    }
                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/p_karya_buku', $filename);
                    $path = $file->storeAs('public/p_karya_buku', $filename);
                    $data['bukti'] = $filename;
                }

                $karyaBuku->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data karya buku berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data karya buku',
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
        $karyaBuku = PKaryaBukuModel::findOrFail($id);
        return view('p_karya_buku.confirm_ajax', compact('karyaBuku'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $karyaBuku = PKaryaBukuModel::findOrFail($id);

        try {
            if ($karyaBuku->bukti && Storage::exists('public/p_karya_buku/' . $karyaBuku->bukti)) {
                Storage::delete('public/p_karya_buku/' . $karyaBuku->bukti);
            }
            $karyaBuku->delete();

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
        $karyaBuku = PKaryaBukuModel::with('user.profile')->findOrFail($id);
        return view('p_karya_buku.detail_ajax', compact('karyaBuku'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $karyaBuku = PKaryaBukuModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $karyaBuku->status = $request->input('status');
            $karyaBuku->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('p_karya_buku.validasi_ajax', compact('karyaBuku'));
    }

    public function import()
    {
        return view('p_karya_buku.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_karya_buku' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_karya_buku');
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
                $isbn = trim($values['D']);

                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $user = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$user) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di data profil user";
                    continue;
                }

                $isDuplicate = PKaryaBukuModel::where('id_user', $user->id_user)
                    ->where('isbn', $isbn)
                    ->exists();

                if ($isDuplicate) {
                    $skippedData[] = "Baris $row: Kombinasi NIDN $nidn dan ISBN $isbn sudah ada.";
                    continue;
                }

                $validator = Validator::make([
                    'id_user' => $user->id_user,
                    'judul_buku' => $values['B'],
                    'tahun' => $values['C'],
                    'isbn' => $isbn,
                    'penerbit' => $values['E'],
                    'jumlah_halaman' => $values['F'],
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'judul_buku' => 'required|string|max:255',
                    'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                    'isbn' => 'required|string|max:50',
                    'penerbit' => 'required|string|max:100',
                    'jumlah_halaman' => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $insertData[] = [
                    'id_user' => $user->id_user,
                    'judul_buku' => $values['B'],
                    'tahun' => $values['C'],
                    'isbn' => $isbn,
                    'penerbit' => $values['E'],
                    'jumlah_halaman' => $values['F'],
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

            $insertedCount = PKaryaBukuModel::insert($insertData);

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
        $query = PKaryaBukuModel::join('user', 'p_karya_buku.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'p_karya_buku.id_karya_buku',
                'profile_user.nama_lengkap as nama_user',
                'p_karya_buku.judul_buku',
                'p_karya_buku.tahun',
                'p_karya_buku.penerbit',
                'p_karya_buku.isbn',
                'p_karya_buku.jumlah_halaman',
                'p_karya_buku.status',
                'p_karya_buku.sumber_data',
                'p_karya_buku.bukti',
                'p_karya_buku.created_at',
                'p_karya_buku.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->getRole();
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_karya_buku.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_karya_buku.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_karya_buku.sumber_data', $sumber);
        }

        $karyaBuku = $query->orderBy('p_karya_buku.id_karya_buku')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Karya Buku');
        $sheet->setCellValue('C1', 'Nama Dosen');
        $sheet->setCellValue('D1', 'Judul Buku');
        $sheet->setCellValue('E1', 'Tahun');
        $sheet->setCellValue('F1', 'Penerbit');
        $sheet->setCellValue('G1', 'ISBN');
        $sheet->setCellValue('H1', 'Jumlah Halaman');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Sumber Data');
        $sheet->setCellValue('K1', 'Bukti');
        $sheet->setCellValue('L1', 'Created At');
        $sheet->setCellValue('M1', 'Updated At');

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($karyaBuku as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->id_karya_buku);
            $sheet->setCellValue('C' . $row, $data->nama_user);
            $sheet->setCellValue('D' . $row, $data->judul_buku);
            $sheet->setCellValue('E' . $row, $data->tahun);
            $sheet->setCellValue('F' . $row, $data->penerbit);
            $sheet->setCellValue('G' . $row, $data->isbn);
            $sheet->setCellValue('H' . $row, $data->jumlah_halaman);
            $sheet->setCellValue('I' . $row, $data->status);
            $sheet->setCellValue('J' . $row, $data->sumber_data);
            if ($data->bukti) {
                $url = url('storage/p_karya_buku/' . $data->bukti);
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

        $sheet->setTitle("Data Karya Buku");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Karya Buku ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PKaryaBukuModel::with('user.profile')->orderBy('id_karya_buku');

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

        $karyaBuku = $query->get();

        $data = $karyaBuku->map(function ($item) {
            return [
                'id_karya_buku' => $item->id_karya_buku,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'judul_buku' => $item->judul_buku,
                'tahun' => $item->tahun,
                'penerbit' => $item->penerbit,
                'isbn' => $item->isbn,
                'jumlah_halaman' => $item->jumlah_halaman,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('p_karya_buku.export_pdf', [
            'karyaBuku' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Karya Buku ' . date('d-m-Y H:i:s') . '.pdf');
    }
}