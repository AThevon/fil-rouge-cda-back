<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
   public function create(User $user, Payment $payment)
   {
      return $payment->order->user_id === $user->id;
   }
}
