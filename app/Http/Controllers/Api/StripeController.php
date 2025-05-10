<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;

class StripeController extends Controller
{
    public function handleSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($request->session_id);

            $email = $session->customer_details->email ?? null;

            if (!$email) {
                return response()->json(['status' => 'error', 'message' => 'Email não encontrado na sessão.'], 422);
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                $password = Str::random(10);
                $user = User::create([
                    'name' => $session->customer_details->name ?? 'Novo Usuário',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'student', // ou como for o padrão
                ]);

                Mail::raw("Bem-vindo ao OmniCursos!\n\nSeu login: {$email}\nSua senha: {$password}\n\nAcesse: https://www.omnicursos.com.br/login", function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Seus dados de acesso - OmniCursos');
                });
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sessão confirmada e usuário registrado!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar a sessão.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
