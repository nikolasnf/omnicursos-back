<?php

namespace App\Services\Stripe;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Invoice as StripeInvoice;
use Stripe\PaymentMethod as StripePaymentMethod;
use App\Models\Subscription;
use App\Models\PaymentMethod;
use App\Models\Invoice;

class SyncStripeService
{
    public static function syncUser($user)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (!$user->email) {
            throw new \Exception('Usuário sem e-mail.');
        }

        // Buscar o Customer pelo email
        $customers = Customer::all([
            'email' => $user->email,
            'limit' => 1,
        ])->data;

        if (count($customers) === 0) {
            throw new \Exception('Cliente não encontrado no Stripe pelo e-mail.');
        }

        $customer = $customers[0];
        $customerId = $customer->id;

        // Buscar assinatura
        $subscriptions = StripeSubscription::all([
            'customer' => $customerId,
            'limit' => 1,
        ])->data;

        if (count($subscriptions)) {
            $sub = $subscriptions[0];

            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_name' => $sub->items->data[0]->plan->nickname ?? 'Plano Padrão',
                    'price' => $sub->items->data[0]->plan->amount / 100,
                    'next_billing_date' => date('Y-m-d', $sub->current_period_end),
                    'status' => $sub->status,
                ]
            );
        }

        // Buscar método de pagamento
        $paymentMethods = StripePaymentMethod::all([
            'customer' => $customerId,
            'type' => 'card',
        ])->data;

        if (count($paymentMethods)) {
            $pm = $paymentMethods[0];

            PaymentMethod::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'brand' => ucfirst($pm->card->brand),
                    'last_digits' => $pm->card->last4,
                    'expiry' => $pm->card->exp_month . '/' . $pm->card->exp_year,
                ]
            );
        }

        // Buscar invoices
        $invoices = StripeInvoice::all([
            'customer' => $customerId,
            'limit' => 20,
        ])->data;

        foreach ($invoices as $invoice) {
            Invoice::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => date('Y-m-d', $invoice->created),
                    'amount' => $invoice->amount_paid / 100,
                ],
                [
                    'file_url' => $invoice->invoice_pdf ?? null,
                ]
            );
        }

        return true;
    }
}
