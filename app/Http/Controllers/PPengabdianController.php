<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PPengabdianModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PPengabdianDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


class PPengabdianController extends Controller
{
    public function index(PPengabdianDataTable $dataTable)
    {
        /** @var \App\Models\UserModel|null $user */
        $user = Auth::user();
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        // Distribusi Skema Pengabdian (pie chart)
        $skemaDistribution = PPengabdianModel::select('skema', DB::raw('count(*) as total'))
            ->groupBy('skema')
            ->orderBy('total', 'desc')
            ->get();

        // Tren Pengabdian Masyarakat Per Tahun (line chart)
        $trenPerTahun = PPengabdianModel::select('tahun', DB::raw('count(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // Peran Dosen dalam Pengabdian (doughnut chart)
        $peranDistribution = PPengabdianModel::select('peran', DB::raw('count(*) as total'))
            ->groupBy('peran')
            ->orderBy('total', 'desc')
            ->get();

        // Keterlibatan Mahasiswa S2 (bar chart)
        $mahasiswaS2Distribution = PPengabdianModel::select('melibatkan_mahasiswa_s2', DB::raw('count(*) as total'))
            ->groupBy('melibatkan_mahasiswa_s2')
            ->orderBy('melibatkan_mahasiswa_s2')
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

        return $dataTable->render('portofolio.pengabdian.index', compact(
            'isAdm', 'isAng', 'isDos',
            'skemaLabels', 'skemaData',
            'trenLabels', 'trenData',
            'peranLabels', 'peranData',
            'mahasiswaS2Labels', 'mahasiswaS2Data'
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

        return view('portofolio.pengabdian.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'judul_pengabdian' => 'required|string|max:255',
                'skema' => 'required|string|max:100',
                'tahun' => 'required|digits:4|integer',
                'dana' => 'required|numeric|min:0',
                'peran' => 'required|in:ketua,anggota',
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

                // Cek duplikasi judul
                $exists = PPengabdianModel::where('id_user', $id_user)
                    ->where('judul_pengabdian', $request->input('judul_pengabdian'))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan judul pengabdian yang sama sudah ada untuk user ini.',
                    ]);
                }

                $data = $request->only([
                    'judul_pengabdian',
                    'skema',
                    'tahun',
                    'dana',
                    'peran',
                    'melibatkan_mahasiswa_s2',
                ]);
                $data['id_user'] = $id_user;

                if ($role === 'DOS') {
                    $data['status'] = 'Tervalidasi';
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
                        $profile = $user->profile;
                        $nidnPrefix = $profile && $profile->nidn ? $profile->nidn . '_' : '';
                    }

                    $originalName = $file->getClientOriginalName();
                    $filename = $nidnPrefix . $originalName;
                    $filename = $this->generateUniqueFilename('public/portofolio/pengabdian', $filename);
                    $file->storeAs('public/portofolio/pengabdian', $filename);
                    $data['bukti'] = $filename;
                }

                PPengabdianModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data pengabdian berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax (Pengabdian): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data pengabdian',
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
        $role = $user ? $user->role : null;

        $pengabdian = PPengabdianModel::findOrFail($id);
        return view('portofolio.pengabdian.edit_ajax', compact('pengabdian', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user ? $user->role : null;

        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'judul_pengabdian' => 'required|string|max:255',
                'skema' => 'required|string|max:100',
                'tahun' => 'required|digits:4|integer',
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
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $pengabdian = PPengabdianModel::findOrFail($id);

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

                // Cek duplikat judul untuk user lain (kecuali record ini sendiri)
                $exists = PPengabdianModel::where('id_user', $id_user)
                    ->where('judul_pengabdian', $request->input('judul_pengabdian'))
                    ->where('id_pengabdian', '!=', $id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'error',
                        'message' => 'Data dengan NIDN dan Judul Pengabdian yang sama sudah ada.',
                    ]);
                }

                $data = $request->only([
                    'judul_pengabdian',
                    'skema',
                    'tahun',
                    'dana',
                    'peran',
                    'melibatkan_mahasiswa_s2',
                ]);

                $data['id_user'] = $id_user;

                if ($role === 'DOS') {
                    $data['status'] = 'Tervalidasi';
                    $data['sumber_data'] = 'dosen';
                } else {
                    $data['status'] = 'perlu validasi';
                    $data['sumber_data'] = 'p3m';
                }

                if ($request->hasFile('bukti')) {
                    // Hapus file lama jika ada
                    if ($pengabdian->bukti && Storage::exists('public/portofolio/pengabdian/' . $pengabdian->bukti)) {
                        Storage::delete('public/portofolio/pengabdian/' . $pengabdian->bukti);
                    }

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
                    $filename = $this->generateUniqueFilename('public/portofolio/pengabdian', $filename);
                    $path = $file->storeAs('public/portofolio/pengabdian', $filename);
                    $data['bukti'] = $filename;
                }

                $pengabdian->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data pengabdian berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengupdate data pengabdian',
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
        $pengabdian = PPengabdianModel::findOrFail($id);
        return view('portofolio.pengabdian.confirm_ajax', compact('pengabdian'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $pengabdian = PPengabdianModel::findOrFail($id);

        try {
            // Hapus file bukti jika ada
            if ($pengabdian->bukti && Storage::exists('public/portofolio/pengabdian/' . $pengabdian->bukti)) {
                Storage::delete('public/portofolio/pengabdian/' . $pengabdian->bukti);
            }

            // Cek apakah model menggunakan SoftDeletes
            if (method_exists($pengabdian, 'forceDelete')) {
                $pengabdian->forceDelete(); // Hapus permanen jika tersedia
            } else {
                $pengabdian->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Data pengabdian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in delete_ajax PPengabdian: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data pengabdian'
            ]);
        }
    }

    public function detail_ajax($id)
    {
        $pengabdian = PPengabdianModel::with('user.profile')->findOrFail($id);
        return view('portofolio.pengabdian.detail_ajax', compact('pengabdian'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $pengabdian = PPengabdianModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $pengabdian->status = $request->input('status');
            $pengabdian->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.pengabdian.validasi_ajax', compact('pengabdian'));
    }

    public function import()
    {
        return view('portofolio.pengabdian.import');
    }

    public function import_ajax(Request $request)
    {
        $request->validate([
            'file_p_pengabdian' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_p_pengabdian');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $insertData = [];
            $updateCount = 0;
            $errors = [];

            $user = Auth::user();
            $role = $user->role ?? null;
            $userNidn = DB::table('profile_user')->where('id_user', $user->id_user)->value('nidn');

            foreach ($data as $row => $values) {
                if ($row == 1) continue; // Skip header

                $nidn = trim($values['A']);
                $judul = trim($values['B']); // Judul Pengabdian
                $skema = trim($values['C']);
                $tahun = trim($values['D']);
                $dana = trim($values['E']);
                $peran = trim($values['F']);
                $melibatkan_mahasiswa_s2 = strtolower(trim($values['G'])) === 'ya' ? 1 : 0;

                // Cek hak akses DOSEN
                if ($role === 'DOS' && $nidn !== $userNidn) {
                    $errors[] = "Baris $row: Anda hanya dapat mengimpor data dengan NIDN milik Anda ($userNidn).";
                    continue;
                }

                $userProfile = DB::table('profile_user')->where('nidn', $nidn)->first();
                if (!$userProfile) {
                    $errors[] = "Baris $row: NIDN $nidn tidak ditemukan di profil user.";
                    continue;
                }

                $validated = Validator::make([
                    'id_user' => $userProfile->id_user,
                    'tahun' => $tahun,
                    'judul_pengabdian' => $judul,
                    'skema' => $skema,
                    'dana' => $dana,
                    'peran' => $peran,
                ], [
                    'id_user' => 'required|integer|exists:user,id_user',
                    'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
                    'judul_pengabdian' => 'required|string|max:255',
                    'skema' => 'required|string|max:255',
                    'dana' => 'required|numeric|min:0',
                    'peran' => 'required|string|max:255',
                ]);

                if ($validated->fails()) {
                    $errors[] = "Baris $row: " . implode(', ', $validated->errors()->all());
                    continue;
                }

                $existing = PPengabdianModel::where('id_user', $userProfile->id_user)
                    ->where('judul_pengabdian', $judul)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'skema' => $skema,
                        'tahun' => $tahun,
                        'dana' => $dana,
                        'peran' => $peran,
                        'melibatkan_mahasiswa_s2' => $melibatkan_mahasiswa_s2,
                    ]);
                    $updateCount++;
                    continue;
                }

                $insertData[] = [
                    'id_user' => $userProfile->id_user,
                    'judul_pengabdian' => $judul,
                    'skema' => $skema,
                    'tahun' => $tahun,
                    'dana' => $dana,
                    'peran' => $peran,
                    'melibatkan_mahasiswa_s2' => $melibatkan_mahasiswa_s2,
                    'status' => $role === 'DOS' ? 'Tervalidasi' : 'perlu validasi',
                    'sumber_data' => $role === 'DOS' ? 'dosen' : 'p3m',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($insertData) && $updateCount === 0) {
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Tidak ada data valid untuk diimpor.' . (count($errors) > 0 ? "\n" . $errors[0] : ''),
                    'showConfirmButton' => true
                ], 422);
            }

            if (!empty($insertData)) {
                PPengabdianModel::insert($insertData);
            }

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Import data berhasil.',
                'inserted_count' => count($insertData),
                'updated_count' => $updateCount,
                'error_count' => count($errors),
                'info' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Import Pengabdian error: ' . $e->getMessage());
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
        $query = PPengabdianModel::join('user', 'p_pengabdian.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_pengabdian.judul_pengabdian',
                'p_pengabdian.skema',
                'p_pengabdian.tahun',
                'p_pengabdian.dana',
                'p_pengabdian.peran',
                'p_pengabdian.melibatkan_mahasiswa_s2',
                'p_pengabdian.status',
                'p_pengabdian.sumber_data',
                'p_pengabdian.bukti',
                'p_pengabdian.created_at',
                'p_pengabdian.updated_at'
            );

        $user = Auth::user();
        $role = $user->role;

        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_pengabdian.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_pengabdian.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_pengabdian.sumber_data', $sumber);
        }

        $pengabdian = $query->orderBy('p_pengabdian.id_pengabdian')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Judul Pengabdian');
        $sheet->setCellValue('D1', 'Tahun');
        $sheet->setCellValue('E1', 'Dana');
        $sheet->setCellValue('F1', 'Peran');
        $sheet->setCellValue('G1', 'Melibatkan Mahasiswa S2');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Sumber Data');
        $sheet->setCellValue('J1', 'Bukti');
        $sheet->setCellValue('K1', 'Created At');
        $sheet->setCellValue('L1', 'Updated At');

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($pengabdian as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->judul_pengabdian);
            $sheet->setCellValue('D' . $row, $data->tahun);
            $sheet->setCellValue('E' . $row, $data->dana);
            $sheet->setCellValue('F' . $row, $data->peran);
            $sheet->setCellValue('G' . $row, $data->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak');
            $sheet->setCellValue('H' . $row, $data->status);
            $sheet->setCellValue('I' . $row, $data->sumber_data);

            if ($data->bukti) {
                $url = url('storage/portofolio/pengabdian/' . $data->bukti);
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

        $sheet->setTitle("Data Pengabdian");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Pengabdian ' . date("Y-m-d H-i-s") . '.xlsx';

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
        $query = PPengabdianModel::with('user.profile')->orderBy('id_pengabdian');

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

        $pengabdian = $query->get();

        $data = $pengabdian->map(function ($item) {
            return [
                'id_pengabdian' => $item->id_pengabdian,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'judul_pengabdian' => $item->judul_pengabdian,
                'skema' => $item->skema,
                'tahun' => $item->tahun,
                'dana' => $item->dana,
                'peran' => $item->peran,
                'melibatkan_mahasiswa_s2' => $item->melibatkan_mahasiswa_s2,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.pengabdian.export_pdf', [
            'pengabdian' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Pengabdian ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
