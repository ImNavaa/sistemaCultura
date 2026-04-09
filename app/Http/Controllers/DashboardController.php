<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        // Módulos del sistema con su configuración
        $todosModulos = [
            [
                'modulo'      => 'calendario',
                'accion'      => 'ver',
                'nombre'      => 'Calendario de Eventos',
                'descripcion' => 'Consulta y gestiona los eventos culturales programados.',
                'icono'       => 'bi-calendar3',
                'color'       => 'primary',
                'ruta'        => 'calendario',
                'params'      => [],
            ],
            [
                'modulo'      => 'oficios',
                'accion'      => 'ver',
                'nombre'      => 'Oficios',
                'descripcion' => 'Administra los oficios oficiales emitidos y recibidos.',
                'icono'       => 'bi-file-earmark-text',
                'color'       => 'info',
                'ruta'        => 'oficios.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'recibos',
                'accion'      => 'ver',
                'nombre'      => 'Recibos',
                'descripcion' => 'Registro y control de recibos generados.',
                'icono'       => 'bi-receipt',
                'color'       => 'success',
                'ruta'        => 'recibos.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'asistencias',
                'accion'      => 'ver',
                'nombre'      => 'Asistencias',
                'descripcion' => 'Control de asistencia y puntualidad del personal.',
                'icono'       => 'bi-person-check',
                'color'       => 'warning',
                'ruta'        => 'asistencias.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'usuarios',
                'accion'      => 'ver',
                'nombre'      => 'Empleados',
                'descripcion' => 'Gestión del personal y sus datos de contacto.',
                'icono'       => 'bi-people',
                'color'       => 'secondary',
                'ruta'        => 'usuarios.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'almacen',
                'accion'      => 'ver',
                'nombre'      => 'Almacén',
                'descripcion' => 'Inventario de materiales y control de existencias.',
                'icono'       => 'bi-box-seam',
                'color'       => 'danger',
                'ruta'        => 'almacen.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'entregas',
                'accion'      => 'ver',
                'nombre'      => 'Entregas',
                'descripcion' => 'Registro de entregas de materiales al personal.',
                'icono'       => 'bi-box-arrow-right',
                'color'       => 'danger',
                'ruta'        => 'entregas.index',
                'params'      => [],
            ],
            [
                'modulo'      => 'tiempo',
                'accion'      => 'ver',
                'nombre'      => 'Control de Tiempo',
                'descripcion' => 'Registro y seguimiento de horas y permisos.',
                'icono'       => 'bi-clock-history',
                'color'       => 'dark',
                'ruta'        => 'tiempo.index',
                'params'      => [],
            ],
        ];

        // Filtrar solo los módulos a los que el usuario tiene acceso
        $modulosPermitidos = array_filter($todosModulos, function ($m) use ($usuario) {
            return $usuario->puede($m['modulo'], $m['accion']);
        });

        return view('dashboard', [
            'usuario'          => $usuario,
            'modulosPermitidos' => array_values($modulosPermitidos),
        ]);
    }
}
