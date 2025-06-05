<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenKriteriaModel;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class DokumenAkhirController extends Controller
{
    public function index()
    {
        // Get all distinct no_kriteria
        $noKriterias = DokumenKriteriaModel::select('no_kriteria')->distinct()->pluck('no_kriteria');

        $mergedHtml = '';
        $allValidated = true;

        // Add CSS for page numbering
        $pageNumberCss = '
            <style>
                @page {
                    margin: 50px 50px 70px 50px;
                }
                body {
                    counter-reset: page;
                }
                .page-number:after {
                    content: counter(page);
                }
                footer {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    height: 5px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                .kategori-title {
                    font-weight: normal;
                    font-size: 20px;
                    margin-top: 10px;
                    margin-bottom: 20px;
                    text-align: center;
                }
            </style>
        ';

        $mergedHtml .= $pageNumberCss;

        // Render cover page
        $mergedHtml .= view('dokumen_akhir.cover')->render();

        $kriteriaList = [];

        foreach ($noKriterias as $noKriteria) {
            // Get latest dokumen_kriteria per kategori with status 'tervalidasi' for this no_kriteria
            $kategoriList = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
            $dokumenKriterias = collect();

            foreach ($kategoriList as $kategori) {
                $dokumen = DokumenKriteriaModel::where('no_kriteria', $noKriteria)
                    ->where('kategori', $kategori)
                    ->where('status', 'tervalidasi')
                    ->orderByDesc('versi')
                    ->first();

                if ($dokumen) {
                    $dokumenKriterias->push($dokumen);
                }
            }

            if ($dokumenKriterias->isEmpty()) {
                $allValidated = false;
                break;
            }

            $judul = $dokumenKriterias->first()->judul ?? '';
            $kriteriaList[] = ['no_kriteria' => $noKriteria, 'judul' => $judul, 'dokumen_kriterias' => $dokumenKriterias];
        }

        if (!$allValidated) {
            return View::make('dokumen_akhir.index')->with('error', 'Dokumen akhir belum bisa dimerge karena ada no_kriteria yang belum tervalidasi.');
        }

        // Prepare daftar isi list with judul and kategori pairs in correct kategori order
        $kategoriOrder = ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'];
        $daftarIsiList = [];
        foreach ($kriteriaList as $kriteria) {
            // Sort dokumen_kriterias by kategori order
            $sortedDokumen = $kriteria['dokumen_kriterias']->sortBy(function ($dok) use ($kategoriOrder) {
                return array_search(ucfirst($dok->kategori), $kategoriOrder);
            });
            foreach ($sortedDokumen as $dokumen) {
                $daftarIsiList[] = [
                    'no_kriteria' => $dokumen->no_kriteria,
                    'judul' => $dokumen->judul,
                    'kategori' => ucfirst($dokumen->kategori),
                    'link' => '#kriteria-' . $dokumen->no_kriteria . '-' . strtolower($dokumen->kategori)
                ];
            }
        }

        // Render daftar isi page with daftarIsiList
        $mergedHtml .= view('dokumen_akhir.daftar_isi', ['daftarIsiList' => $daftarIsiList])->render();

        foreach ($kriteriaList as $kriteria) {
            $dokumenKriterias = $kriteria['dokumen_kriterias'];

            // Render separator page with judul and kategori

            foreach ($dokumenKriterias as $dokumen) {
                $kategori = ucfirst($dokumen->kategori);
                // Render separator page with judul and kategori
                $mergedHtml .= view('dokumen_akhir.separator', [
                    'no_kriteria' => $dokumen->no_kriteria,
                    'judul' => $dokumen->judul,
                    'kategori' => $kategori
                ])->render();

                $contentHtml = $dokumen->content_html;

                // Replace relative storage URLs with full asset URLs
                $contentHtml = preg_replace_callback('/href="storage\/([^"]+)"/i', function ($matches) {
                    $relativePath = $matches[1];
                    $url = asset('storage/' . $relativePath);
                    return 'href="' . $url . '"';
                }, $contentHtml);

                $mergedHtml .= $contentHtml;
            }
        }

        if (!$allValidated) {
            return View::make('dokumen_akhir.index')->with('error', 'Dokumen akhir belum tersedia');
        }

        // Add footer with page number
        $mergedHtml .= '<footer class="page-number"></footer>';

        // Generate PDF from mergedHtml with bookmarks
        $pdf = Pdf::loadHTML($mergedHtml)->setPaper('a4', 'portrait');

        // Add bookmarks/outlines for each kriteria and kategori
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $y = 800; // initial y position for bookmarks, adjust as needed

        // Note: DomPDF does not provide a direct API for bookmarks,
        // so this is a placeholder for where you would add bookmarks if supported.
        // Alternatively, consider using a PDF library with better bookmark support.

        $pdfPath = public_path('pdfs/dokumen_akhir.pdf');
        $pdf->save($pdfPath);

        $pdfUrl = asset('pdfs/dokumen_akhir.pdf');

        return View::make('dokumen_akhir.index')->with('pdfUrl', $pdfUrl);
    }
}
