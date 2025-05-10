<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3UploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('arquivo')) {
            return response()->json(['erro' => 'Nenhum arquivo enviado'], 400);
        }

        $file = $request->file('arquivo');
        $path = Storage::disk('s3')->put('cursos', $file); // pasta "cursos" no bucket
        $url = Storage::disk('s3')->url($path);

        return response()->json([
            'message' => 'Upload realizado com sucesso!',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
