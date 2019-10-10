<?php
namespace App\Repositories\ContactForm;

use App\Models\ContactForm;
use Illuminate\Http\Request;

interface ContactFormInterface
{
    /**
     * Store contact form details
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\ContactForm;
     */
    public function store(Request $request): ContactForm;
}
