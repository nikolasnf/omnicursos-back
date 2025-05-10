<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;

class StripeController extends Controller
{
    public function checkout()
    {
        return view('stripe.checkout'); // Exemplo de Blade simples
    }

    public function processPayment(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $charge = Charge::create([
            'amount' => 5000, // em centavos (R$ 50,00)
            'currency' => 'brl',
            'description' => 'Pagamento OmniCursos',
            'source' => $request->stripeToken,
            'receipt_email' => $request->email,
        ]);

        return response()->json(['message' => 'Pagamento realizado com sucesso!']);
    }
}
