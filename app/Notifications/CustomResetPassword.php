<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Pokédex password')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'appName' => config('app.name', 'Pokédex'),
            ])
            ->withSymfonyMessage(function ($message) {
                $message->embedFromPath(public_path('images/logo.png'), 'logo');
            });
    }
}