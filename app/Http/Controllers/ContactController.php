<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactMail;
use App\Mail\ContactUserMail;

class ContactController extends Controller
{
   public function send(Request $request)
   {
      // Validation des données du formulaire
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'email' => 'required|email',
         'message' => 'required|string|min:10',
      ]);

      if ($validator->fails()) {
         return response()->json(['errors' => $validator->errors()], 422);
      }

      // Préparer les données pour l'email
      $emailData = [
         'name' => $request->name,
         'email' => $request->email,
         'message' => $request->message,
      ];

      // Envoyer les emails
      Mail::to(config('mail.from.address'))->send(new ContactMail($emailData));
      Mail::to($request->email)->send(new ContactUserMail($emailData));

      return response()->json(['message' => 'Votre message a été envoyé avec succès.'], 200);
   }
}