<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;

class StripeWebhookController extends Controller
{
   public function handle(Request $request)
   {
      Stripe::setApiKey(config('services.stripe.secret'));

      $payload = $request->getContent();
      $sigHeader = $request->header('Stripe-Signature');
      $endpointSecret = config('services.stripe.webhook_secret');

      try {
         $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
      } catch (\UnexpectedValueException $e) {
         return response()->json(['error' => 'Invalid payload'], 400);
      } catch (\Stripe\Exception\SignatureVerificationException $e) {
         return response()->json(['error' => 'Invalid signature'], 400);
      }

      // Gérer les événements Stripe
      switch ($event->type) {
         case 'checkout.session.completed':
            $session = $event->data->object;

            // Trouvez la commande associée via `client_reference_id`
            $order = Order::where('id', $session->client_reference_id)->first();

            if ($order) {
               // Mettez à jour le statut de la commande
               $order->update(['status' => OrderStatus::COMPLETED]);

               // Créez un enregistrement de paiement
               Payment::create([
                  'order_id' => $order->id,
                  'stripe_session_id' => $session->id,
                  'stripe_payment_intent_id' => $session->payment_intent,
                  'amount' => $session->amount_total / 100,
                  'currency' => $session->currency,
                  'status' => PaymentStatus::COMPLETED,
               ]);
            }
            break;

         case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;

            $order = Order::where('id', $paymentIntent->metadata->order_id)->first();

            if ($order) {
               $order->update(['status' => OrderStatus::CANCELED]);
            }
            break;

         default:
            // Événements non pris en charge
            break;
      }

      return response()->json(['status' => 'success']);
   }
}