<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\Stripe\SyncStripeService;

class HomeController extends Controller
{
    /**
     * Retorna cursos aleatórios para exibir na home.
     *
     * @return \Illuminate\Http\Response
     */
    public function featuredCourses()
    {



//	$user = User::find(27); // ou qualquer user_id
//	SyncStripeService::syncUser($user);


        $courses = Course::select('id', 'title', 'thumbnail', 'description')
            ->inRandomOrder()
            ->take(10) // quantidade de cursos que deseja exibir
	    ->where('status','published')
            ->get();

        return response()->json($courses);
    }
}
