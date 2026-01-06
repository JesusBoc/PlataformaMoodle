<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_academicload_install() {
    global $DB;

    // Verifica si el rol ya existe
    if ($DB->record_exists('role', ['shortname' => 'docente_global'])) {
        return;
    }

    // Crear rol
    $roleid = create_role(
        'Docente global',
        'docente_global',
        'Rol base para identificar docentes a nivel sistema'
    );

    // Asignar capability propia del plugin
    assign_capability(
        'local/academicload:docenteglobal',
        CAP_ALLOW,
        $roleid,
        context_system::instance()->id
    );

    // Capabilities mínimas útiles
    assign_capability(
        'moodle/course:view',
        CAP_ALLOW,
        $roleid,
        context_system::instance()->id
    );

    assign_capability(
        'moodle/course:update',
        CAP_ALLOW,
        $roleid,
        context_system::instance()->id
    );
}
