<?php

namespace App\Http\Controllers;

use App\Models\DokumenKriteriaModel;
use Illuminate\Http\Request;
use App\Models\ProfileUser;
use Illuminate\Support\Facades\Auth;

class ValidasiController extends Controller
{
    public function index(){
        return view('validasi.index');
    }
public function showFile(Request $request)
    {
        try {
            $kriteria = $request->kriteria;
            
            if (!$kriteria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kriteria tidak boleh kosong'
                ]);
            }
            $validasi = DokumenKriteriaModel::where('id_dokumen_kriteria', $kriteria)->first();
            // Generate path PDF berdasarkan kriteria
            $pdfFileName = "kriteria_{$kriteria}.pdf";
            
            // Path sebenarnya di public/pdfs (untuk pengecekan file exists)
            $pdfPath = public_path("pdfs/{$pdfFileName}");
            
            // URL untuk akses public
            $pdfUrl = asset("pdfs/{$pdfFileName}");
           
            // Check apakah file PDF ada
            if (file_exists($pdfPath)) {
                return response()->json([
                    'success' => true,
                    'pdfUrl' => $pdfUrl,
                    'kriteria' => $kriteria,
                    'fileName' => $pdfFileName,
                    'status' => $validasi->status ?? null,
                    'komentar' => $validasi->komentar ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "PDF untuk kriteria {$kriteria} tidak ditemukan di: {$pdfPath}"
                ]);
            }
           
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function valid(Request $request){
        $user = Auth::user();
        $idValidator = ProfileUser::where('id_user', $user->id_user)->value('id_profile');
        $kriteria = $request->kriteria;
        $status = $request->status;
        $check = DokumenKriteriaModel::where('id_dokumen_kriteria',$kriteria)->first();

        if (!$check) {
            return response()->json([
                    'success' => false,
                    'message' => "Data data kriteria tidak ditemukan didalam system"
                ]);
        }
        $check->update(
            [
                'id_validator'=>$idValidator,
                'status'=> $status,
                'komentar'=>'',
            'updated_at'=>now()
            ]);
        return response()->json([
                    'success' => true,
                    'message' => "Berhasil Menyimpan"
                ]);
    }
    public function store(Request $request){
        $user = Auth::user();
        $idValidator = ProfileUser::where('id_user', $user->id_user)->value('id_profile');
        $kriteria = $request->kriteria;
        $status = $request->status;
        $komentar = $request->komentar;
        $check = DokumenKriteriaModel::where('id_dokumen_kriteria',$kriteria)->first();
        if (!$check) {
            return response()->json([
                    'success' => false,
                    'message' => "PDF untuk kriteria data kriteria tidak ditemukan"
                ]);
        }
        $check->update([
            'id_validator'=>$idValidator,
            'status'=> $status,
            'komentar'=>$komentar,
            'updated_at'=>now()
        ]);
        return response()->json([
                    'success' => true,
                    'message' => "Berhasil Menyimpan"
                ]);
    }
}
