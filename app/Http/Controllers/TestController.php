<?php

namespace App\Http\Controllers;

use App\Helper\CommonFunction;
use Illuminate\Http\Request;
use Mail;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $entryLimit;
    private $entryDate;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->entryLimit = 10;
        $this->entryDate = date("Y-m-d H:i:s");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function testMailPage(){

        return view('send-test-email');
    }

    public function sendTestEmail(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'email' => 'required|email',
            'description' => 'required|string',
        ]);

        // Prepare the data to send in the email
        $data = [
            'email' => $validated['email'],
            'description' => $validated['description'], // This should match the variable used in the Blade view
        ];

        // Send the email directly from the controller
        Mail::send('emails.testMail', $data, function($message) use ($validated) {
            $message->to($validated['email'])
                    ->subject('New Message');
        });

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
