<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * Basis-controller voor alle HTTP-controllers in de applicatie.
 *
 * Controllers delegeren businesslogica naar Services en gebruiken
 * FormRequest-klassen voor server-side validatie.
 */
abstract class Controller extends BaseController
{
    //
}
