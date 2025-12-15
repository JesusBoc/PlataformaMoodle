<?php
// This is a DEVELOPMENT TEST SCRIPT. Do not use in production.


require_once(__DIR__ . '/../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

use local_curriculum\model\area;
use local_curriculum\model\subject;

echo "<pre>";
echo "=== INICIO PRUEBAS MODELO CURRICULUM ===\n\n";

// --------------------------------------------------
// 1. Obtener plan activo existente
// --------------------------------------------------
global $DB;

$planrecord = $DB->get_record('local_curriculum_plan', ['active' => 1], '*', MUST_EXIST);
$planid = $planrecord->id;

echo "✔ Plan activo encontrado: ID {$planid}\n";

// --------------------------------------------------
// 2. Crear área
// --------------------------------------------------
$areaid = area::create([
    'planid'     => $planid,
    'areaname'   => 'Área de prueba',
    'sortorder'  => 1
]);

echo "✔ Área creada con ID {$areaid}\n";

// --------------------------------------------------
// 3. Crear asignatura con área
// --------------------------------------------------
$subjectid = subject::create([
    'planid'      => $planid,
    'subjectname' => 'Asignatura de prueba',
    'areaid'      => $areaid,
    'ihs'         => 4,
    'sortorder'   => 1
]);

echo "✔ Asignatura creada con ID {$subjectid}\n";

// --------------------------------------------------
// 4. Obtener asignaturas por plan
// --------------------------------------------------
$subjects = subject::get_by_plan($planid);
echo "✔ Asignaturas del plan:\n";

foreach ($subjects as $s) {
    echo "  - {$s->id} | {$s->subjectname} | areaid={$s->areaid}\n";
}

// --------------------------------------------------
// 5. Quitar asignatura del área (desasignar)
// --------------------------------------------------
subject::update($subjectid, [
    'areaid' => null
]);

echo "✔ Asignatura desasignada del área\n";

// --------------------------------------------------
// 6. Eliminar área (asignaturas quedan sin área)
// --------------------------------------------------
area::delete($areaid);

echo "✔ Área eliminada\n";

// --------------------------------------------------
// 7. Eliminar asignatura
// --------------------------------------------------
subject::delete($subjectid);

echo "✔ Asignatura eliminada\n";

// --------------------------------------------------
// 8. Prueba delete_by_plan (sin efectos colaterales)
// --------------------------------------------------
area::delete_by_plan($planid);
subject::delete_by_plan($planid);

echo "✔ delete_by_plan ejecutado (si no había registros, no pasa nada)\n";

// --------------------------------------------------
echo "\n=== FIN PRUEBAS ===\n";
echo "</pre>";

