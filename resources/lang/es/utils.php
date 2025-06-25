<?php

return [
    'gender' => [
        'm' => 'Masculino',
        'f' => 'Femenino'
    ],
    'office_type' => [
        '1' => 'Consultorio Independiente',
        '2' => 'Clínica Privada',
        '3' => 'Laboratorio',

    ],
    'status_hacienda_color' => [
        'aceptado' => 'success',
        'recibido' => 'warning',
        'procesando' => 'warning',
        'rechazado' => 'danger',
        'error' => 'danger'
    ],
    'tipo_documento_color' => [
        '01' => 'success',
        '02' => 'warning',
        '03' => 'danger',
        '04' => 'info',
        '05' => 'info',
        '06' => 'info',
        '07' => 'info',
        '08' => 'info'
    ],
    'tipo_documento' => [
        '01' => 'Factura electrónica',
        '02' => 'Nota débito electrónica',
        '03' => 'Nota crédito electrónica',
        '04' => 'Tiquete electrónico',
        '05' => 'Nota de despacho',
        '06' => 'Contrato',
        '07' => 'Procedimiento',
        '08' => 'Comprobante emitido en contingencia',
        '99' => 'Otro'
    ],

    'medio_pago' => [
        '01' => 'Efectivo',
        '02' => 'Tarjeta',
       
    ],
    'condicion_venta' => [
        '01' => 'Contado',
        '02' => 'Crédito',

    ],
    'codigo_referencia' => [
        '01' => 'Anula Documento de Referencia',
        '02' => 'Corrige texto documento de referencia',
        '03' => 'Corrige monto',
        '04' => 'Referencia a otro documento',
        '05' => 'Sustituye comprobante provisional por contingencia',
        '99' => 'Otro',

    ],
    'provincias' => [
        '1' => 'San José',
        '2' => 'Alajuela',
        '3' => 'Cartago',
        '4' => 'Heredia',
        '5' => 'Guanacaste',
        '6' => 'Puntarenas',
        '7' => 'Limón',

    ],
    'provincias_colors' => [
        '1' => 'success',
        '2' => 'secondary',
        '3' => 'danger',
        '4' => 'info',
        '5' => 'warning',
        '6' => 'purple',
        '7' => 'maroon',

    ],
    'status_appointments_name' => [
        'reservedByMedic' => 'Reservadas por Médico',
        'reservedByPatient' => 'Reservadas por Pacientes',
        'attended' => 'Atendidas',
        'noassist' => 'No Asistió',
    ],
    'status_appointment_requests_name' => [
        'reserved' => 'Reservadas',
        'scheduled' => 'Agendadas',
        'pending' => 'Pendientes',
        'cancelled' => 'Canceladas',
    ],
    'status_appointments_name_color' => [
        'reservedByMedic' => 'info',
        'reservedByPatient' => 'secondary',
        'attended' => 'success',
        'noassist' => 'danger',
    ],
    'status_appointment_requests_name_color' => [
        'reserved' => 'info',
        'scheduled' => 'success',
        'pending' => 'secondary',
        'cancelled' => 'danger',
    ],
    'status_appointments' => [
        '0' => 'Reservadas',
        '1' => 'Atendidas',
        '2' => 'No Asistió',
    ],
    'status_appointments_color' => [
        '0' => 'secondary',
        '1' => 'success',
        '2' => 'danger',
        '3' => 'info'
       
    ],
    'status_medics' => [
        '0' => 'Inactivos',
        '1' => 'Activos',
       
    ],
    'status_medics_color' => [
        '0' => 'danger',
        '1' => 'success',
       

    ],
    'tipo_mensaje_receptor' => [
        '1' => 'Aceptado',
        '2' => 'Aceptado Parcialmente',
        '3' => 'Rechazado',
        
    ],
    'codigo_tarifa_name' => [
        '00' => 'Sin Impuesto (Exento)',
        '01' => 'Tarifa 0% (Exento)',
        '02' => 'Tarifa reducida 1%',
        '03' => 'Tarifa reducida 2%',
        '04' => 'Tarifa reducida 4%',
        '05' => 'Transitorio 0%',
        '06' => 'Transitorio 4%',
        '07' => 'Transitorio 8%',
        '08' => 'Tarifa general 13%',
    ],

];
