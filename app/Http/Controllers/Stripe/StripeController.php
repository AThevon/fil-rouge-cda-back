<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    /**
     * Créer une session de paiement Stripe Checkout pour plusieurs articles.
     */
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Créer les line items pour chaque produit dans le panier
        $lineItems = [];
        foreach ($request->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product_name'],
                    ],
                    'unit_amount' => $item['amount'] * 100, // Convertir le montant en centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Créez la session Stripe Checkout avec les line items
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => env('FRONTEND_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('FRONTEND_URL') . '/payment/cancel',
        ]);

        // Retourner l'ID de session au frontend
        return response()->json(['id' => $session->id]);
    }
}