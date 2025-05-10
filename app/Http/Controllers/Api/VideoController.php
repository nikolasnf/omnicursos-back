<?php

namespace App\Http\Controllers\Api;

ini_set('memory_limit', '1024M');


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VideoController extends Controller
{
    public function proxyS3(Request $request)
    {
    	$user = auth()->user();

//    	if (!$user) {
  //      	return redirect('/login');
    //	}
        $path = $request->query('path');

        if (!$path) {
            return response()->json(['erro' => 'Parâmetro "path" obrigatório.'], 400);
        }

        $path = ltrim($path, '/');

        // Monta a URL do S3
        $s3Url = "https://omnicursos-storage.s3.us-east-1.amazonaws.com/" . rawurlencode($path);

        // Faz a requisição ao S3 usando Laravel HTTP Client
        $resposta = Http::withHeaders([
            'Accept' => '*/*'
        ])->get($s3Url);

        if (!$resposta->successful()) {
            return response("Erro ao acessar conteúdo S3", $resposta->status());
        }

        // Captura tipo de conteúdo e dados
        $conteudo = $resposta->body();
        $tipo = $resposta->header('Content-Type', 'application/octet-stream');

        return Response::make($conteudo, 200, [
            'Content-Type' => $tipo,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
