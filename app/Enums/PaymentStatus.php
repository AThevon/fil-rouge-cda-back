<?php

namespace App\Enums;

enum PaymentStatus: string
{
   case PENDING = 'pending'; // En attente
   case AUTHORIZED = 'authorized'; // Autorisé mais pas encore capturé
   case COMPLETED = 'completed'; // Paiement réussi
   case FAILED = 'failed'; // Paiement échoué
   case CANCELED = 'canceled'; // Paiement annulé
   case REFUNDED = 'refunded'; // Paiement remboursé
   case DISPUTED = 'disputed'; // Paiement contesté
   case EXPIRED = 'expired'; // Session expirée
}