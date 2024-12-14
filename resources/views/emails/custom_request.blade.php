<!DOCTYPE html>
<html lang="fr">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Nouvelle demande personnalisée</title>
   <style>
      /* Styles généraux */
      body {
         font-family: Arial, sans-serif;
         line-height: 1.6;
         margin: 0;
         padding: 0;
         background-color: #f4f4f9;
         color: #333;
      }

      .email-container {
         max-width: 600px;
         margin: 20px auto;
         background-color: #ffffff;
         border: 1px solid #ddd;
         border-radius: 8px;
         overflow: hidden;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }

      .header {
         background-color: #4caf50;
         color: #ffffff;
         text-align: center;
         padding: 20px;
         font-size: 24px;
         font-weight: bold;
      }

      .content {
         padding: 20px;
      }

      .content h1 {
         font-size: 20px;
         margin-bottom: 10px;
         color: #4caf50;
      }

      .content p {
         margin: 10px 0;
      }

      .content strong {
         color: #333;
      }

      .footer {
         background-color: #f4f4f9;
         text-align: center;
         padding: 10px;
         font-size: 12px;
         color: #777;
         border-top: 1px solid #ddd;
      }

      /* Bouton stylé pour les actions */
      .button {
         display: inline-block;
         margin: 20px 0;
         padding: 10px 20px;
         background-color: #4caf50;
         color: #ffffff;
         text-decoration: none;
         font-size: 16px;
         border-radius: 4px;
      }

      .button:hover {
         background-color: #45a049;
      }
   </style>
</head>

<body>
   <div class="email-container">
      <!-- En-tête -->
      <div class="header">
         Nouvelle demande personnalisée
      </div>

      <!-- Contenu -->
      <div class="content">
         <h1>Détails de la demande :</h1>
         <p><strong>Utilisateur :</strong> {{ $data['user_name'] }}</p>
         <p><strong>Email :</strong> {{ $data['user_email'] }}</p>
         <p><strong>Catégorie :</strong> {{ $data['category'] }}</p>
         <p><strong>Message :</strong></p>
         <p>{{ $data['message'] }}</p>

         @if (empty($data['images']))
          <p>Aucune image associée à cette demande.</p>
       @endif
      </div>

      <!-- Pied de page -->
      <div class="footer">
         <p>Cet email a été généré automatiquement. Merci de ne pas y répondre.</p>
         <p>&copy; 2024 Wooden Factory. Tous droits réservés.</p>
      </div>
   </div>
</body>

</html>