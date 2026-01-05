<?php
require_once('../../config.php');

use local_coursebuilder\service\CourseCreationService;

require_sesskey();
require_login();

$context = context_system::instance();
require_capability('local/coursebuilder:buildcourse', $context);

$actions = $SESSION->coursebuilder_actionmap ?? null;
$categoryid = $SESSION->coursebuilder_categoryid ?? null;

if (!$actions or !$categoryid) {
    throw new moodle_exception('No hay datos de ejecución');
}

$service = new CourseCreationService();
$result = $service->execute($actions, $categoryid);

// Limpieza
unset($SESSION->coursebuilder_actionmap);

redirect(
    new moodle_url('/local/coursebuilder/index.php'),
    'Cursos creados correctamente',
    null,
    \core\output\notification::NOTIFY_SUCCESS
);