<x-mail::message>
# ¡Registro confirmado!

Hola **{{ $asistente->nombre }}**, tu registro para la siguiente actividad ha sido recibido exitosamente.

---

## {{ $actividad->nombre }}

<x-mail::panel>
**Folio de registro:** {{ $inscripcion->folio }}
</x-mail::panel>

@if($actividad->fecha_inicio)
📅 **Fecha:** {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') }}@if($actividad->fecha_fin && $actividad->fecha_fin != $actividad->fecha_inicio) al {{ \Carbon\Carbon::parse($actividad->fecha_fin)->translatedFormat('d \d\e F \d\e Y') }}@endif

@endif
@if($actividad->hora_inicio)
🕐 **Horario:** {{ substr($actividad->hora_inicio, 0, 5) }}{{ $actividad->hora_fin ? ' — ' . substr($actividad->hora_fin, 0, 5) : '' }} hrs

@endif
@if($actividad->ubicacion)
📍 **Lugar:** {{ $actividad->ubicacion }}

@endif
@if($actividad->instructor)
👤 **Instructor:** {{ $actividad->instructor }}

@endif
@if($actividad->modalidad)
💻 **Modalidad:** {{ ucfirst($actividad->modalidad) }}

@endif

---

@if(filled($actividad->requisitos))
## 📋 Requisitos para participar

Por favor lee con atención los siguientes requisitos antes de asistir:

<x-mail::panel>
{!! nl2br(e($actividad->requisitos)) !!}
</x-mail::panel>

> **Importante:** Asegúrate de cumplir con todos los requisitos. Sin ellos podrías no poder participar.

---

@endif

Guarda tu folio de registro: **{{ $inscripcion->folio }}**

Te lo pedirán al momento de registrar tu asistencia el día del evento.

<x-mail::button :url="url('/registro/' . $actividad->id)" color="blue">
Ver detalles de la actividad
</x-mail::button>

¡Te esperamos pronto!

Dirección de Cultura
</x-mail::message>
