<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;
use App\Models\Category;

class CourseController extends Controller
{
    // Lista todos os cursos com contagem de módulos e aulas
    public function index()
    {
        $courses = Course::withCount(['modules', 'modules as lessons_count' => function ($query) {
            $query->join('lessons', 'modules.id', '=', 'lessons.module_id');
        }])->get();

        return response()->json($courses);
    }

    /**
     * Retorna os cursos de uma categoria baseada no slug.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function getByCategory(Request $request, $slug)
    {
        // Busca a categoria pelo slug
        $category = Category::where('slug', $slug)->with(['courses' => function($query) {
            $query->select('id', 'title', 'slug', 'thumbnail', 'status', 'external_link', 'category_id');
        }])->first();

        if (!$category) {
            return response()->json([
                'error' => 'Categoria não encontrada.'
            ], 404);
        }

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'courses' => $category->courses
        ]);
    }


    // Retorna dados de um curso específico com seus módulos e aulas
    public function show($id)
    {
        $course = Course::with('modules.lessons')->findOrFail($id);

        return response()->json($course);
    }

    // Cria um novo curso
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|string',
            'external_link' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
        ]);

        $course = Course::create($validated);

        return response()->json(['message' => 'Curso criado com sucesso!', 'course' => $course], 201);
    }

    // Atualiza um curso existente
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|string',
            'external_link' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'ord' => 'nullable|numeric|min:0',
        ]);

        $course->update($validated);

        return response()->json(['message' => 'Curso atualizado com sucesso!', 'course' => $course]);
    }

    // Deleta um curso
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(['message' => 'Curso deletado com sucesso.']);
    }

    // Retorna os módulos de um curso com aulas
    public function getModulesByCourse($id)
    {
        $modules = Module::where('course_id', $id)
            ->with(['lessons' => function ($query) {
                $query->orderBy('ord');
            }])
	    ->orderBy('ord')
            ->get();

        foreach ($modules as &$mod) {
            foreach ($mod['lessons'] as &$lesson) {
                $lesson['resource_url'] = $lesson['content_url'];
                $lesson['type'] = $lesson['content_type'];
            }
        }

        return response()->json($modules);
    }

    public function getBySlug($slug)
    {
        $course = Course::where('slug', $slug)->first();
	$course->modules = $this->getModulesByCourse($course->id);
        $course->category = Category::where('id', $course->category_id)->first()->name;

	return $course;
    }

    public function indexWithCounts()
    {
        $courses = Course::withCount([
            'modules',
            'modules as lessons_count' => function ($query) {
                $query->join('lessons', 'modules.id', '=', 'lessons.module_id');
            }
        ])->get();

        return response()->json($courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'slug' => $course->slug,
                'category' => Category::where('id', $course->category_id)->first()->name,
                'category_id' => $course->category_id,
                'status' => $course->status,
                'modules_count' => $course->modules_count,
                'lessons_count' => $course->lessons_count,
            ];
        }));

    }

    public function updateCourseWithModules(Request $request, $id)
    {
/*
    $request->validate([
        'modules' => 'array',
        'modules.*.title' => 'required|string|max:255',
        'modules.*.lessons' => 'array',
        'modules.*.lessons.*.title' => 'required|string|max:255',
        'modules.*.lessons.*.content_type' => 'required|in:video,pdf,link',
        'modules.*.lessons.*.duration' => 'nullable|string',
        'modules.*.lessons.*.content_url' => 'nullable|string',
    ]);
*/
    $course = Course::findOrFail($id);
    $course->modules = Course::with('modules.lessons')->findOrFail($id);


    // Deleta os módulos e aulas antigos
//    foreach ($course->modules as $module) {
//        $module->lessons()->delete();
//    }
    $course->modules()->delete();

    // Cria os novos módulos e aulas
    foreach ($request->modules as $mod) {
        $module = $course->modules()->create([
            'title' => $mod['title'],
            'ord' => $mod['ord'],
        ]);
        foreach ($mod['lessons'] as $lesson) {
            $module->lessons()->create([
                'title' => $lesson['title'],
                'duration' => $lesson['duration'],
                'description' => $lesson['description'],
                'content_url' => $lesson['resource_url'],
                'content_type' => $lesson['type'],
                'ord' => $lesson['ord'],
            ]);
        }
    }

    return response()->json([
        'message' => 'Curso e módulos atualizados com sucesso.',
    ]);

}

public function getCourseContent(Request $request, $id)
{
    $user = $request->user(); // usado para exibir nome e favorito

    $course = Course::with(['modules.lessons'])
        ->find($id);

    if (!$course) {
        return response()->json(['error' => 'Curso não encontrado.'], 404);
    }

    return response()->json([
        'course' => [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'status' => $course->status,
            'is_favorite' => false, // adaptar se quiser ter favoritos
            'modules' => $course->modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'lessons' => $module->lessons->map(function ($lesson) {
                        return [
                            'id' => $lesson->id,
                            'title' => $lesson->title,
                            'duration' => $lesson->duration,
                            'content_type' => $lesson->content_type,
                            'content_url' => $lesson->content_url,
                            'description' => $lesson->description,
                        ];
                    }),
                ];
            }),
        ]
    ]);
}




}
