<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserFavorite;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $userId = Auth::id();
        $courseId = $request->input('course_id');

        $favorite = UserFavorite::where('user_id', $userId)
                                ->where('course_id', $courseId)
                                ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['favorited' => false]);
        }

        UserFavorite::create([
            'user_id' => $userId,
            'course_id' => $courseId
        ]);

        return response()->json(['favorited' => true]);
    }

    public function listFavorites()
    {
        $userId = Auth::id();

        $courses = Course::whereIn('id', function($query) use ($userId) {
            $query->select('course_id')
                  ->from('user_favorites')
                  ->where('user_id', $userId);
        })->get();

        return response()->json($courses);
    }

    public function isFavorite($courseId)
    {
        $userId = Auth::id();

        $exists = UserFavorite::where('user_id', $userId)
                              ->where('course_id', $courseId)
                              ->exists();

        return response()->json(['favorited' => $exists]);
    }
}
