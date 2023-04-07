<?php

namespace App\Mail;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IngredientAlert extends Mailable
{
    use Queueable, SerializesModels;

      // The ingredient that is low in stock
      public  $ingredient;

    /**
     * Create a new message instance.
     */

    public function __construct($ingredient)
    {

        $this->ingredient = $ingredient;
    }

    /**
     * Build the message.
     */

    // Return a view with the ingredient data
    public function build()
    {
        return $this->subject('Ingredient Alert')->view('emails.ingredient_alert');
    }
}
