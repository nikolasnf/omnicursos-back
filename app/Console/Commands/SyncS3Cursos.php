<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Category;

class SyncS3Cursos extends Command
{
    protected $signature = 's3:sync-cursos';
    protected $description = 'Sincroniza apenas os cursos com base nas pastas do S3';

    public function handle()
    {
    $categorias = Storage::disk('s3')->directories('/');

    foreach ($categorias as $bucketRoot) {
        $categoriaNome = trim($bucketRoot, '/');
        $categoria = Category::where('slug', Str::slug($categoriaNome))->first();

        if (!$categoria) {
            $this->error("⚠️ Categoria não encontrada: $categoriaNome");
            continue;
        }
        $pastasCursos = Storage::disk('s3')->directories($bucketRoot);
        foreach ($pastasCursos as $pasta) {
            $partes = explode('/', $pasta);
            if (count($partes) < 2) continue;

            $cursoNome = end($partes);

            Course::updateOrCreate(
                ['title' => $cursoNome],
                [
                    'category_id' => $categoria->id,
                    'slug' => Str::slug($cursoNome),
                    'path' => $pasta
                ]
            );

            $this->line("✔️ Curso encontrado: $cursoNome – categoria: {$categoria->name}");
        }
    }

    $this->info('✅ Sincronização de cursos concluída.');
    }


}
