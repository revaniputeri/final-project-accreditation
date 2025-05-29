<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/img');
            $url = Storage::url($path);

            return response()->json(['location' => $url]);
        }

        return response()->json(['error' => 'Tidak ada file terupload'], 400);
    }
}
