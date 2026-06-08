<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\StoreContactRequest;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(StoreContactRequest $request)
    {
        Contact::create($request->validated());

        return redirect()->route('contact.index')->with('success', 'Tu mensaje ha sido enviado correctamente. ¡Gracias por contactarnos!');
    }
}
