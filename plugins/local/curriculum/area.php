<?php

require_once('../../config.php');
require_login();

use local_curriculum\form\area_form;
use local_curriculum\service\AreaManager;

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$planid = required_param('planid', PARAM_INT);
$areaid = optional_param('id', 0, PARAM_INT);  // Obtener el ID del area (si la hay)
$area = null;

$PAGE->set_url('/local/curriculum/area.php', ['planid' => $planid]);
$PAGE->set_context($context);

$title = get_string('createarea', 'local_curriculum');

if ($areaid) {
    $title = get_string('editarea', 'local_curriculum');
    $area = AreaManager::get_by_id($areaid);
    if (!$area || $area->planid != $planid) {
        throw new moodle_exception('invalidarea', 'local_curriculum');
    }
}
$PAGE->set_title($title);
$PAGE->set_heading($title);

$form = new area_form(null, 
                ['area' => $area,
                'planid' => $planid]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/curriculum/index.php'));
}

if ($data = $form->get_data()) {
    if (!empty($data->id)) {
        AreaManager::update_area($data->id, $data->areaname);
    } else {
        AreaManager::create_area($data->planid, $data->areaname);
    }
    redirect(new moodle_url('/local/curriculum/index.php'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();