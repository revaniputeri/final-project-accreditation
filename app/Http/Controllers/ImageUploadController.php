<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('public/img');
                $url = Storage::url($path);

                return response()->json(['location' => $url]);
            }

            return response()->json(['error' => 'Tidak ada file terupload'], 400);
        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengupload gambar'], 500);
        }
    }
}
