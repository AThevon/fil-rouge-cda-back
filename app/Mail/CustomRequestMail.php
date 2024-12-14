<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomRequestMail extends Mailable
{
   use Queueable, SerializesModels;

   public $data;
   private $temporaryFiles = [];

   /**
    * Create a new message instance.
    */
   public function __construct(array $data)
   {
      $this->data = $data;
   }

   /**
    * Get the message envelope.
    */
   public function envelope(): Envelope
   {
      return new Envelope(
         subject: 'Nouvelle demande personnalisée'
      );
   }

   /**
    * Get the message content definition.
    */
   public function content(): Content
   {
      return new Content(
         view: 'emails.custom_request',
         with: ['data' => $this->data]
      );
   }

   /**
    * Get the attachments for the message.
    *
    * @return array<int, Attachment>
    */
   public function attachments(): array
   {
      $attachments = [];

      if (!empty($this->data['images'])) {
         foreach ($this->data['images'] as $imageUrl) {
            $tempFile = tempnam(sys_get_temp_dir(), 'attachment_');
            file_put_contents($tempFile, file_get_contents($imageUrl));

            $attachments[] = Attachment::fromPath($tempFile)
               ->as(basename($imageUrl)) // Définit le nom du fichier
               ->withMime(mime_content_type($tempFile)); // Définit le type MIME

            // Ajoute le fichier temporaire à la liste
            $this->temporaryFiles[] = $tempFile;
         }
      }

      return $attachments;
   }

   public function __destruct()
   {
      // Supprime les fichiers temporaires
      foreach ($this->temporaryFiles as $file) {
         if (file_exists($file)) {
            unlink($file);
         }
      }
   }
}