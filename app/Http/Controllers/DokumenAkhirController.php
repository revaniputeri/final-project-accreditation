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
            </style>
        ';

        $mergedHtml .= $pageNumberCss;

        // Render cover page
        $mergedHtml .= view('dokumen_akhir.cover')->render();

        $kriteriaList = [];

        foreach ($noKriterias as $noKriteria) {
            // Get latest versi with status 'tervalidasi' for this no_kriteria
            $latestDokumen = DokumenKriteriaModel::where('no_kriteria', $noKriteria)
                ->where('status', 'tervalidasi')
                ->orderByDesc('versi')
                ->first();

            if (!$latestDokumen) {
                $allValidated = false;
                break;
            }

            $judul = $latestDokumen->judul ?? '';
            $kriteriaList[] = ['no_kriteria' => $noKriteria, 'judul' => $judul];
        }

        if (!$allValidated) {
            return View::make('dokumen_akhir.index')->with('error', 'Dokumen akhir belum bisa dimerge karena ada no_kriteria yang belum tervalidasi.');
        }

        // Render daftar isi page with kriteriaList
        $mergedHtml .= view('dokumen_akhir.daftar_isi', ['kriteriaList' => $kriteriaList])->render();

        foreach ($kriteriaList as $kriteria) {
            $noKriteria = $kriteria['no_kriteria'];
            $judul = $kriteria['judul'];

            // Render separator page with judul and anchor id
            $mergedHtml .= view('dokumen_akhir.separator', ['no_kriteria' => $noKriteria, 'judul' => $judul])->render();

            // Add content_html
            $latestDokumen = DokumenKriteriaModel::where('no_kriteria', $noKriteria)
                ->where('status', 'tervalidasi')
                ->orderByDesc('versi')
                ->first();

            $contentHtml = $latestDokumen->content_html;

            // Replace file:// URLs with asset URLs
            $contentHtml = $latestDokumen->content_html;

            // Replace relative storage URLs with full asset URLs
            $contentHtml = preg_replace_callback('/href="storage\/([^"]+)"/i', function ($matches) {
                $relativePath = $matches[1];
                $url = asset('storage/' . $relativePath);
                return 'href="' . $url . '"';
            }, $contentHtml);

            $mergedHtml .= $contentHtml;
        }

        if (!$allValidated) {
            return View::make('dokumen_akhir.index')->with('error', 'Dokumen akhir belum tersedia');
        }

        // Add footer with page number
        $mergedHtml .= '<footer class="page-number"></footer>';

        // Generate PDF from mergedHtml
        $pdf = Pdf::loadHTML($mergedHtml)->setPaper('a4', 'portrait');

        $pdfPath = public_path('pdfs/dokumen_akhir.pdf');
        $pdf->save($pdfPath);

        $pdfUrl = asset('pdfs/dokumen_akhir.pdf');

        return View::make('dokumen_akhir.index')->with('pdfUrl', $pdfUrl);
    }
}
