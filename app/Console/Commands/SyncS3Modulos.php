<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Module;

class SyncS3Modulos extends Command
{
    protected $signature = 's3:sync-modulos';
    protected $description = 'Sincroniza os módulos a partir do path de cada curso';

    public function handle()
    {
        $cursos = Course::all();

        foreach ($cursos as $curso) {

            $cursoPath = $curso->path . '/';

            if (!Storage::disk('s3')->exists($cursoPath)) {
                $this->warn("⚠ Pasta não encontrada para o curso: $curso->title ($cursoPath)");
                continue;
            }

            $modulos = Storage::disk('s3')->directories($cursoPath);
            foreach ($modulos as $moduloPath) {
                $nomeModulo = basename($moduloPath);
                $module = Module::updateOrCreate([
                    'course_id' => $curso->id,
                    'title' => $nomeModulo,
                ], [
                    'path' => $moduloPath,
                    'parent' => 0
                ]);

                $this->line("✔ Módulo criado: $nomeModulo (curso: $curso->title)");

		$childs = Storage::disk('s3')->directories($moduloPath);
                foreach ($childs as $child) {
                	$nomeModulo = basename($child);
	                Module::updateOrCreate([
        	            'course_id' => $curso->id,
                	    'title' => $nomeModulo,
                	], [
                    	    'path' => $child,
                    	    'parent' => $module->id
                	]);

	                $this->line("✔ Módulo criado: $nomeModulo (curso: $curso->title)");
		}


            }




/*

            foreach ($modulos as $moduloPath) {
                $nomeModulo = basename($moduloPath);

                Module::updateOrCreate([
                    'course_id' => $curso->id,
                    'title' => $nomeModulo,
                ], [
                    'path' => $moduloPath,
                    'parent' => 0
                ]);

                $this->line("✔ Módulo criado: $nomeModulo (curso: $curso->title)");
            }
*/
        }

        $this->info('✅ Sincronização de módulos concluída.');
    }
}
