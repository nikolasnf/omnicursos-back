<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Str;

class MarketingCoursesSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que a categoria "Marketing" existe
        $category = Category::firstOrCreate(
            ['slug' => 'marketing'],
            ['name' => 'Marketing']
        );

        $courses = [
            ['Método Resultado - Alex Vargas', 'Um dos cursos mais vendidos no Brasil, referência em afiliados e estruturação digital.'],
            ['Mentoria do Desbloqueio ao Milhão MDM - Pablo Marçal', 'Altíssimo apelo emocional e autoridade em marketing e mentalidade.'],
            ['João Castanheira', 'Grande nome no mercado de afiliados, cursos com boas provas sociais.'],
            ['Joba', 'Influente no digital, cursos práticos e com marketing afiado.'],
            ['Afiliado Orgânico 2.0 - Leonardo David', 'Método conhecido e buscado por iniciantes sem verba para tráfego pago.'],
            ['Afiliado Viking - Marcelo Tavora', 'Branding forte, linguagem jovem e direta.'],
            ['Cientista do Marketing V4 - Dener Lippert', 'Curso premium da V4 Company, autoridade crescente no Brasil.'],
            ['Alex Vargas', 'Em qualquer curso, é nome de peso.'],
            ['Método Midas - Adriana Sant’Anna', 'Curso voltado para público feminino com apelo de lifestyle e riqueza.'],
            ['Insta Pro - Hyesser Wesley Silva', 'Instagram ainda é foco de quem quer crescer digitalmente, curso promissor.'],
            ['Instagram Rocket - Santiago Paz Zapata', 'Alta conversão para quem busca explosão de seguidores e engajamento.'],
            ['Autoridade Top das Galáxias - Ana Liz', 'Nome memorável, focado em autoridade digital.'],
            ['Clientes Infinitos - Pedro Superti', 'Curso que ensina diferenciação, copy, branding — muito buscado.'],
            ['Como Fazer Vendas Ativas - Thiago Nigro', 'Nome forte, mesmo com conteúdo simples, o branding do Primo Rico ajuda.'],
            ['Máquina de Vendas Online - Mentor Borges', 'Proposta clara e direta, ótima para públicos iniciantes.'],
            ['Método TikTok Lucrativo - Mustache', 'TikTok em alta, ótimo para atrair público jovem.'],
            ['Método Instamap - Gabriel Porfírio', 'Curso bem estruturado e com foco direto em estratégias práticas.'],
            ['Método Super Conteúdo - Max Peters', 'Conteúdo é rei no digital, e o nome já diz tudo.'],
            ['Checklist SEO - Rafael Rez', 'Público mais técnico, mas com valor de longo prazo.'],
            ['Curso Aprenda Neuromarketing - Arthur Parede e Rejane Toigo', 'Alta percepção de valor, diferencial para criadores de conteúdo e copywriters.']
        ];

        foreach ($courses as [$title, $description]) {
            Course::create([
                'title' => $title,
                'description' => $description,
                'thumbnail' => null,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'category_id' => $category->id,
                'status' => 'published'
            ]);
        }
    }
}
