<?php

namespace App\Mail;

use App\Models\Actividad;
use App\Models\Asistente;
use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmacionRegistroMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Asistente  $asistente,
        public Actividad  $actividad,
        public Inscripcion $inscripcion,
    ) {}

    public function envelope(): Envelope
    {
        $tieneRequisitos = filled($this->actividad->requisitos);

        return new Envelope(
            subject: $tieneRequisitos
                ? '✅ Registro confirmado + Requisitos — ' . $this->actividad->nombre
                : '✅ Registro confirmado — ' . $this->actividad->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.confirmacion-registro',
            with: [
                'asistente'   => $this->asistente,
                'actividad'   => $this->actividad,
                'inscripcion' => $this->inscripcion,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
