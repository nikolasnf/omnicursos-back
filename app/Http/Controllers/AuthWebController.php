<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'error' => 'Credenciais inválidas'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Limpa a sessão (opcional, útil para browser/cookies)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

}
