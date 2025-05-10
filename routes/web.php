<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\VideoController;

Route::get('/api/password-reset', function (Request $request) {
    $token = $request->query('token');
    $email = $request->query('email');

    return redirect()->away("https://www.omnicursos.com.br/login?token={$token}&email={$email}");
});

Route::get('/checkout', [StripeController::class, 'checkout']);
Route::post('/checkout', [StripeController::class, 'processPayment']);

