<?php

namespace App\Actions;

use Illuminate\Support\Facades\Mail;

class MailAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function send($email, $content, $view, $subject){
        Mail::send($view, ['content' => $content], function ($message) use ($email, $subject) {
            $message->to($email);
            $message->subject($subject);
        });
    }
}
