<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PProfesiModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\DataTables\PProfesiDataTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PProfesiController extends Controller
{
    public function index(PProfesiDataTable $dataTable)
    {
        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->role;
        $isAdm = $user->hasRole('ADM');
        $isDos = $user->hasRole('DOS');
        $isAng = $user->hasRole('ANG');

        // Distribution of Gelar Akademik (overall)
        $gelarDistribution = PProfesiModel::select('gelar', DB::raw('count(*) as total'))
            ->groupBy('gelar')
            ->orderBy('total', 'desc')
            ->get();

        // Distribution of Perguruan Tinggi (for bar chart)
        $perguruanTinggiDistribution = PProfesiModel::select('perguruan_tinggi', DB::raw('count(*) as total'))
            ->groupBy('perguruan_tinggi')
            ->orderBy('total', 'desc')
            ->get();

        // Prepare data arrays for charts
        $gelarLabels = $gelarDistribution->pluck('gelar');
        $gelarData = $gelarDistribution->pluck('total');

        $perguruanTinggiLabels = $perguruanTinggiDistribution->pluck('perguruan_tinggi');
        $perguruanTinggiData = $perguruanTinggiDistribution->pluck('total');

        return $dataTable->render('portofolio.profesi.index', compact(
            'isAdm', 'isAng', 'isDos',
            'gelarLabels', 'gelarData',
            'perguruanTinggiLabels', 'perguruanTinggiData'
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

        return view('portofolio.profesi.create_ajax', compact('dosens', 'role'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'perguruan_tinggi' => 'required|string|max:255',
                'kurun_waktu' => 'required|string|max:100',
                'gelar' => 'required|string|max:100',
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
                        'message' => 'ID user tidak ditemukan. Pastikan akun user terkait.',
                    ]);
                }

                $data = $request->only([
                    'perguruan_tinggi',
                    'kurun_waktu',
                    'gelar',
                ]);

                $data['id_user'] = $id_user;

                // Set status dan sumber_data
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

                // Upload file bukti jika ada
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
                    $filename = $this->generateUniqueFilename('public/portofolio/profesi', $filename);
                    $path = $file->storeAs('public/portofolio/profesi', $filename);
                    $data['bukti'] = $filename;
                }

                PProfesiModel::create($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data profesi berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in store_ajax PProfesi: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal menyimpan data profesi',
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

        $profesi = PProfesiModel::findOrFail($id);
        return view('portofolio.profesi.edit_ajax', compact('profesi', 'dosens', 'role'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            /** @var UserModel|null $user */
            $user = Auth::user();
            $role = $user ? $user->getRole() : null;

            $rules = [
                'perguruan_tinggi' => 'required|string|max:255',
                'kurun_waktu' => 'required|string|max:100',
                'gelar' => 'required|string|max:100',
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

            $profesi = PProfesiModel::findOrFail($id);

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
                        'message' => 'ID user tidak ditemukan. Pastikan akun user terkait.',
                    ]);
                }

                $data = $request->only([
                    'perguruan_tinggi',
                    'kurun_waktu',
                    'gelar',
                ]);

                // Set status dan sumber_data
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

                // Upload file bukti jika ada
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
                    $filename = $this->generateUniqueFilename('public/portofolio/profesi', $filename);
                    $path = $file->storeAs('public/portofolio/profesi', $filename);
                    $data['bukti'] = $filename;
                }

                PProfesiModel::where('id_profesi', $id)->update($data);

                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Data profesi berhasil diperbarui'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in update_ajax PProfesi: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal memperbarui data profesi',
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
        $profesi = PProfesiModel::findOrFail($id);
        return view('portofolio.profesi.confirm_ajax', compact('profesi'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $profesi = PProfesiModel::findOrFail($id);

        try {
            if ($profesi->bukti && Storage::exists('public/portofolio/profesi/' . $profesi->bukti)) {
                Storage::delete('public/portofolio/profesi/' . $profesi->bukti);
            }
            $profesi->delete();

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
        $profesi = PProfesiModel::with('user.profile')->findOrFail($id);
        return view('portofolio.profesi.detail_ajax', compact('profesi'));
    }

    public function validasi_ajax(Request $request, $id)
    {
        $profesi = PProfesiModel::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'status' => 'required|in:tervalidasi,tidak valid',
            ]);

            $profesi->status = $request->input('status');
            $profesi->save();

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        }

        return view('portofolio.profesi.validasi_ajax', compact('profesi'));
    }

    public function import()
    {
        return view('portofolio.profesi.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            try {
                $file = $request->file('file');
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                // Proses data dari sheet
                foreach ($sheetData as $row) {
                    // Asumsikan kolom pertama adalah perguruan_tinggi, kedua kurun_waktu, ketiga gelar, keempat bukti
                    if (count($row) < 4) {
                        continue; // Lewati baris yang tidak lengkap
                    }

                    PProfesiModel::create([
                        'perguruan_tinggi' => $row[0],
                        'kurun_waktu' => $row[1],
                        'gelar' => $row[2],
                        'bukti' => isset($row[3]) ? $row[3] : null,
                        'status' => 'perlu validasi',
                        'sumber_data' => 'p3m',
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimpor'
                ]);
            } catch (\Exception $e) {
                Log::error('Exception in import_ajax PProfesi: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'alert' => 'error',
                    'message' => 'Gagal mengimpor data',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'alert' => 'error',
            'message' => 'Request tidak valid'
        ], 400);
    }

    public function export_excel()
    {
        $query = PProfesiModel::join('user', 'p_profesi.id_user', '=', 'user.id_user')
            ->join('profile_user', 'user.id_user', '=', 'profile_user.id_user')
            ->select(
                'profile_user.nama_lengkap as nama_user',
                'p_profesi.perguruan_tinggi',
                'p_profesi.kurun_waktu',
                'p_profesi.gelar',
                'p_profesi.status',
                'p_profesi.sumber_data',
                'p_profesi.bukti',
                'p_profesi.created_at',
                'p_profesi.updated_at'
            );

        /** @var UserModel|null $user */
        $user = Auth::user();
        $role = $user->role;
        if ($role === 'DOS' && $user->id_user) {
            $query->where('p_profesi.id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('p_profesi.status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('p_profesi.sumber_data', $sumber);
        }

        $profesi = $query->orderBy('p_profesi.id_profesi')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Dosen');
        $sheet->setCellValue('C1', 'Perguruan Tinggi');
        $sheet->setCellValue('D1', 'Kurun Waktu');
        $sheet->setCellValue('E1', 'Gelar');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Sumber Data');
        $sheet->setCellValue('H1', 'Bukti');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $no = 1;
        $row = 2;
        foreach ($profesi as $data) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $data->nama_user);
            $sheet->setCellValue('C' . $row, $data->perguruan_tinggi);
            $sheet->setCellValue('D' . $row, $data->kurun_waktu);
            $sheet->setCellValue('E' . $row, $data->gelar);
            $sheet->setCellValue('F' . $row, $data->status);
            $sheet->setCellValue('G' . $row, $data->sumber_data);

            if ($data->bukti) {
                $url = url('storage/portofolio/profesi/' . $data->bukti);
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

        $sheet->setTitle("Data Profesi");

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Profesi ' . date("Y-m-d H-i-s") . '.xlsx';

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
        /** @var UserModel|null $user */
        $user = Auth::user();

        $query = PProfesiModel::with('user.profile')->orderBy('id_profesi');

        if ($user->hasRole('DOS') && $user->id_user) {
            $query->where('id_user', $user->id_user);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($sumber = request('filter_sumber')) {
            $query->where('sumber_data', $sumber);
        }

        $profesi = $query->get();

        $data = $profesi->map(function ($item) use ($user) {
            $isDos = $user->hasRole('DOS');
            return [
                'id_profesi' => $item->id_profesi,
                'nama_dosen' => $item->user && $item->user->profile ? $item->user->profile->nama_lengkap : '-',
                'perguruan_tinggi' => $item->perguruan_tinggi,
                'kurun_waktu' => $item->kurun_waktu,
                'gelar' => $item->gelar,
                'status' => $item->status,
                'sumber_data' => $item->sumber_data,
                'bukti' => $item->bukti,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        $pdf = Pdf::loadView('portofolio.profesi.export_pdf', [
            'profesi' => $data
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', base_path('public'));

        return $pdf->stream('Data Profesi ' . date('d-m-Y H:i:s') . '.pdf');
    }
}
