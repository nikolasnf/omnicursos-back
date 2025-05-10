<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProgress;

class ProgressController extends Controller
{
public function markCompleted(Request $request)
{
    $data = $request->validate([
        'course_id' => 'required|exists:courses,id',
        'module_id' => 'nullable|exists:modules,id',
        'lesson_id' => 'required|exists:lessons,id',
    ]);

    $progress = UserProgress::updateOrCreate(
        [
            'user_id' => auth()->id(),
            'lesson_id' => $data['lesson_id'],
        ],
        [
            'course_id' => $data['course_id'],
            'module_id' => $data['module_id'],
            'completed' => true,
            'completed_at' => now(),
        ]
    );

    return response()->json(['status' => 'success', 'progress' => $progress]);
}

public function getProgress($course_id)
{
    $progress = UserProgress::where('user_id', auth()->id())
        ->where('course_id', $course_id)
        ->where('completed', true)
        ->get(['lesson_id', 'completed_at']);

    return response()->json(['progress' => $progress]);
}



}
