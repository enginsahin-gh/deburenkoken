<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HomeController extends Controller
{
    private Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function index(): View
    {
        return view('index');
    }

    public function info(): View
    {
        return view('info');
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function contactForm(): RedirectResponse
    {
        $this->request->validate([
            'name' => 'required',
            'email' => ['required', 'email:rfc,dns', 'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
            'phone_number' => ['nullable', 'numeric', 'digits_between:9,15'],
            'question' => ['required'],
        ]);

        $data = [
            'name' => $this->request->input('name'),
            'phone' => $this->request->input('phone_number'),
            'email' => $this->request->input('email'),
            'msg' => $this->request->input('question'),
            'content' => 'Ingevulde opmerking:',
        ];

        $adminData = $data;
        $adminData['admin'] = true;

        Mail::send('emails.contact', $adminData, function ($message) {
            $message
                ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->to(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->subject('Je vraag is ontvangen');
        });

        Mail::send('emails.contact', $data, function ($message) {
            $message
                ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->to($this->request->input('email'), $this->request->input('name'))
                ->subject('Je vraag is ontvangen');
        });

        return redirect()->route('contact.success');
    }

    public function contactSuccess(): View
    {
        return view('contact-send');
    }

    public function customerFacts(): View
    {
        return view('customer-facts');
    }

    public function cookFacts(): View
    {
        return view('cook-facts');
    }

    public function cookTips(): View
    {
        return view('cook-tips');
    }

    public function terms(): View
    {
        return view('terms');
    }

    public function privacy(): View
    {
        return view('privacy');
    }

    public function cookie(): View
    {
        return view('cookie');
    }
}
