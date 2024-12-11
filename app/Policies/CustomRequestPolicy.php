<?php 

namespace App\Policies;

use App\Models\CustomRequest;
use App\Models\User;

class CustomRequestPolicy
{
    /**
     * Autorise l'affichage de toutes les requêtes (index).
     */
    public function viewAny(User $user)
    {
        // Autoriser uniquement les utilisateurs connectés.
        return $user !== null;
    }

    /**
     * Autorise l'affichage d'une requête spécifique.
     */
    public function view(User $user, CustomRequest $customRequest)
    {
        // L'utilisateur doit être le propriétaire de la requête.
        return $user->id === $customRequest->user_id;
    }

    /**
     * Autorise la création d'une requête.
     */
    public function create(User $user)
    {
        // Tous les utilisateurs connectés peuvent créer une requête.
        return $user !== null;
    }

    /**
     * Autorise la mise à jour d'une requête.
     */
    public function update(User $user, CustomRequest $customRequest)
    {
        // L'utilisateur doit être le propriétaire de la requête.
        return $user->id === $customRequest->user_id;
    }

    /**
     * Autorise la suppression d'une requête.
     */
    public function delete(User $user, CustomRequest $customRequest)
    {
        // L'utilisateur doit être le propriétaire de la requête.
        return $user->id === $customRequest->user_id;
    }
}