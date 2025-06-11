<?php

namespace App\Http\Controllers;

use App\Models\DokumenKriteriaModel;
use Illuminate\Http\Request;
use App\Models\ProfileUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class ValidasiController extends Controller
{
    public function index()
    {
        // Get unique no_kriteria only, ignoring versi and kategori
        $dokumenKriteria = DokumenKriteriaModel::select('no_kriteria', 'judul')
            ->groupBy('no_kriteria', 'judul')
            ->get();

        return view('validasi.index', compact('dokumenKriteria'));
    }

    public function showFile(Request $request)
    {
        try {
            $kriteria = $request->kriteria;
            $kategori = $request->kategori;

            if (!$kriteria || !$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kriteria dan kategori tidak boleh kosong'
                ]);
            }

            $dokumen = DokumenKriteriaModel::where('no_kriteria', $kriteria)
                ->where('kategori', $kategori)
                ->latest('versi')->first();

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen kriteria tidak ditemukan'
                ]);
            }

            // Setup Dompdf options
            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);

            // Fix image URLs in content_html by embedding images as base64
            $contentHtml = $dokumen->content_html;

            // Use DOMDocument to parse and replace image src with base64 data
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML(mb_convert_encoding($contentHtml, 'HTML-ENTITIES', 'UTF-8'));

            // Fix image <img src="storage/img/..."> to base64
            foreach ($dom->getElementsByTagName('img') as $img) {
                $src = $img->getAttribute('src');
                if (preg_match('#(?:/)?storage/img/(.+)$#', $src, $matches)) {
                    $path = public_path('storage/img/' . $matches[1]);
                    if (file_exists($path)) {
                        $type = mime_content_type($path);
                        $data = base64_encode(file_get_contents($path));
                        $base64 = "data:$type;base64,$data";
                        $img->setAttribute('src', $base64);
                    }
                }
            }

            // Replace hyperlinks href to local file paths for dompdf
            $anchors = $dom->getElementsByTagName('a');
            foreach ($anchors as $a) {
                $href = $a->getAttribute('href');

                // Handle both formats:
                // 1. http://127.0.0.1:8000/storage/dokumen_pendukung/...
                // 2. /storage/dokumen_pendukung/...
                if (preg_match('/^(https?:\/\/127\.0\.0\.1:8000)?(\/storage\/dokumen_pendukung\/.+)$/', $href, $matches)) {
                    $relativePath = $matches[2]; // This will capture the /storage/... part in both cases
                    $localPath = public_path(ltrim($relativePath, '/'));

                    if (file_exists($localPath)) {
                        // Pastikan URL yang dihasilkan bisa diakses publik
                        $a->setAttribute('href', asset($relativePath));
                        $a->setAttribute('target', '_blank');
                    }
                }
            }

            $contentHtml = $dom->saveHTML($dom->documentElement);


            // Load fixed content_html to Dompdf
            $dompdf->loadHtml($contentHtml);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF
            $dompdf->render();

            // Output the PDF as base64 string
            $output = $dompdf->output();
            $base64Pdf = 'data:application/pdf;base64,' . base64_encode($output);

            return response()->json([
                'success' => true,
                'pdfUrl' => $base64Pdf,
                'kriteria' => $kriteria,
                'kategori' => $kategori,
                'status' => $dokumen->status ?? null,
                'komentar' => $dokumen->komentar ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function valid(Request $request)
    {
        $user = Auth::user();
        $idValidator = $user->id; // Use user id for foreign key
        $kriteria = $request->kriteria;
        $kategori = $request->kategori;
        $status = $request->status;
        $check = DokumenKriteriaModel::where('no_kriteria', $kriteria)
            ->where('kategori', $kategori)
            ->latest('versi')->first();

        if (!$check) {
            return response()->json([
                'success' => false,
                'message' => "Data data kriteria tidak ditemukan didalam system"
            ]);
        }
        $check->update(
            [
                'id_validator' => $idValidator,
                'status' => $status,
                'komentar' => '',
                'updated_at' => now()
            ]
        );
        return response()->json([
            'success' => true,
            'message' => "Berhasil Menyimpan"
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $idValidator = $user->id; // Use user id for foreign key
            $kriteria = $request->kriteria;
            $kategori = $request->kategori;
            $status = $request->status;
            $komentar = $request->komentar;
            $check = DokumenKriteriaModel::where('no_kriteria', $kriteria)
                ->where('kategori', $kategori)
                ->latest('versi')->first();
            if (!$check) {
                return response()->json([
                    'success' => false,
                    'message' => "PDF untuk kriteria data kriteria tidak ditemukan"
                ]);
            }
            $check->update([
                'id_validator' => $idValidator,
                'status' => $status,
                'komentar' => $komentar,
                'updated_at' => now()
            ]);
            return response()->json([
                'success' => true,
                'message' => "Berhasil Menyimpan"
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ValidasiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saat menyimpan data validasi: ' . $e->getMessage()
            ]);
        }
    }

    public function getDokumenInfo(Request $request)
    {
        $noKriteria = $request->no_kriteria;
        $kategori = $request->kategori;
        if (!$noKriteria || !$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor kriteria dan kategori tidak boleh kosong'
            ]);
        }
        $dokumen = DokumenKriteriaModel::where('no_kriteria', $noKriteria)
            ->where('kategori', $kategori)
            ->latest('versi')->first();
        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen kriteria tidak ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'no_kriteria' => $dokumen->no_kriteria,
                'judul' => $dokumen->judul,
                'versi' => $dokumen->versi,
                'status' => $dokumen->status,
                'updated_at' => $dokumen->updated_at ? $dokumen->updated_at->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }
}
