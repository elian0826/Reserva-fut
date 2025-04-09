<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de Contraseña')
            ->line('Estás recibiendo este correo porque hemos recibido una solicitud de recuperación de contraseña para tu cuenta.')
            ->action('Recuperar Contraseña', $url)
            ->line('Este enlace de recuperación expirará en 60 minutos.')
            ->line('Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna acción adicional.');
    }
} 