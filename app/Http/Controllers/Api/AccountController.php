<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function plan(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'plan' => [
                'name' => optional($user->subscription)->plan_name ?? '',
                'price' => optional($user->subscription)->price ?? 0,
                'next_billing_date' => optional($user->subscription)->next_billing_date ?? null,
                'status' => optional($user->subscription)->status ?? '',
                'benefits' => [
                    'Acesso a mais de 500 cursos premium',
                    'Novos cursos adicionados semanalmente',
                    'Certificados de conclusão',
                    'Acesso em qualquer dispositivo',
                ]
            ],
            'payment_method' => [
                'brand' => optional($user->paymentMethod)->brand ?? '',
                'last_digits' => optional($user->paymentMethod)->last_digits ?? '',
                'expiry' => optional($user->paymentMethod)->expiry ?? '',
            ],
            'invoices' => $user->invoices ? $user->invoices->take(3)->map(function ($invoice) {
                return [
                    'month_year' => $invoice->date->format('F Y'),
                    'date' => $invoice->date->format('d/m/Y'),
                    'amount' => $invoice->amount,
                    'download_url' => $invoice->file_url ?? null,
                ];
            }) : [],
        ]);
    }
}
