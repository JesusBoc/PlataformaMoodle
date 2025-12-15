<?php
// This is a DEVELOPMENT TEST SCRIPT. Do not use in production.


require_once(__DIR__ . '/../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

use local_curriculum\service\PlanManager;
use local_curriculum\service\AreaManager;
use local_curriculum\service\SubjectManager;
use core\exception\moodle_exception;


echo "<pre>";
echo "=== INICIO PRUEBAS MODELO CURRICULUM ===\n\n";

// --------------------------------------------------
// 1. Obtener plan activo existente
// --------------------------------------------------
global $DB;

$transaction = $DB->start_delegated_transaction();

$planrecord = PlanManager::get_active_by_category(1);
$planid = $planrecord->id;

echo "✔ Plan activo encontrado: ID {$planid}\n";

// --------------------------------------------------
// 2. Crear área
// --------------------------------------------------
$areaid = AreaManager::create_area($planid, 'Matematicas');

echo "✔ Área creada con ID {$areaid}\n";

// --------------------------------------------------
// 3. Crear asignatura con área
// --------------------------------------------------
$subjectid1 = SubjectManager::create_subject($planid, 'Algebra', $areaid, 2);
echo "✔ Asignatura creada con ID {$subjectid1}\n";
$subjectid2 = SubjectManager::create_subject($planid, 'Geometria', $areaid, 2);
echo "✔ Asignatura creada con ID {$subjectid2}\n";
$subjectid3 = SubjectManager::create_subject($planid, 'Catedra de la paz', null, 2);
echo "✔ Asignatura creada con ID {$subjectid3}\n";

// --------------------------------------------------
// 4. Obtener asignaturas por plan
// --------------------------------------------------
$subjects = SubjectManager::get_by_plan($planid);
echo "✔ Asignaturas del plan:\n";

foreach ($subjects as $s) {
    echo "  - {$s->id} | {$s->subjectname} | areaid={$s->areaid}\n";
}

print_r(PlanManager::validate_plan($planid));

// --------------------------------------------------
// 5. Quitar asignatura del área (desasignar)
// --------------------------------------------------
SubjectManager::update_area($subjectid2);

$subjects = SubjectManager::get_by_plan($planid);

foreach ($subjects as $s) {
    echo "  - {$s->id} | {$s->subjectname} | areaid={$s->areaid}\n";
}


echo "✔ Asignatura desasignada del área\n";

// --------------------------------------------------
// 6. Eliminar área (asignaturas quedan sin área)
// --------------------------------------------------
AreaManager::delete_area($areaid);

echo "✔ Área eliminada\n";

echo "✔ Asignaturas del plan:\n";

$subjects = SubjectManager::get_by_plan($planid);

foreach ($subjects as $s) {
    echo "  - {$s->id} | {$s->subjectname} | areaid={$s->areaid}\n";
}

print_r(PlanManager::validate_plan($planid));


$transaction->rollback(new moodle_exception('rollback'));
// --------------------------------------------------
echo "\n=== FIN PRUEBAS ===\n";
echo "</pre>";

