<?php
// This is a DEVELOPMENT TEST SCRIPT. Do not use in production.

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/curriculum/lib.php');

echo "=== Adding subject test ===\n\n";

// 🔹 1. Ajusta este ID a una categoría real de tu Moodle
$categoryid = 1;

// 🔹 2. Intentar obtener plan activo
$activeplan = local_curriculum_get_active_plan_by_category($categoryid);

if ($activeplan) {
    echo "Active plan found:\n";
    print_r($activeplan);
} else {
    echo "No active plan found for category {$categoryid}\n";
    die;
}

// 🔹 3. Crear nueva asignatura
echo "\nCreating new subject...\n";
$newsubjectid = local_curriculum_add_subject(1,'Asignatura de prueba', 1);

echo "New subject created with ID: {$newsubjectid}\n";

// 🔹 5. Obtener asignaturas (debe estar vacío por ahora)
$subjects = local_curriculum_get_plan_subjects($activeplan->id);

echo "\nSubjects in active plan:\n";
print_r($subjects);

echo "\n=== Test finished ===\n";
