<?php

namespace App\Repositories\ContactForm;

use App\Models\ContactForm;
use App\Repositories\ContactForm\ContactFormInterface;
use Illuminate\Http\Request;

class ContactFormRepository implements ContactFormInterface
{
    /**
     *
     * @var App\Models\ContactForm
     */
    private $contactForm;

    /**
     * Create a new contact form repository instance.
     *
     * @param  App\Models\ContactForm $contactForm
     * @return void
     */
    public function __construct(
        ContactForm $contactForm
    ) {
        $this->contactForm = $contactForm;
    }

    /**
     * Store contact form details
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\ContactForm;
     */
    public function store(Request $request): ContactForm
    {
        $storyVisitorDataArray = array(
            'user_id' => $request->auth->user_id,
            'phone_no' => $request->phone_no,
            'message' => strip_tags($request->message),
        );

        $storyVisitorData = $this->contactForm->create($storyVisitorDataArray);
        return $storyVisitorData;
    }
}
