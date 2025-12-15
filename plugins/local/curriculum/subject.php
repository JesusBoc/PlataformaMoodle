<?php

require_once('../../config.php');
require_login();

use local_curriculum\form\subject_form;
use local_curriculum\service\SubjectManager;
use local_curriculum\service\AreaManager;

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$planid = required_param('planid', PARAM_INT);
$subjectid = optional_param('id', 0, PARAM_INT);  // Obtener el ID de la asignatura (si la hay)
$subject = null;
$areasraw = AreaManager::get_by_plan($planid);
$areas = [];

foreach ($areasraw as $area) {
    $areas[$area->id] = $area->name;
}

$PAGE->set_url('/local/curriculum/subject.php', ['planid' => $planid]);
$PAGE->set_context($context);
$title = get_string('createsubject', 'local_curriculum');

if ($subjectid) {
    $title = get_string('editsubject', 'local_curriculum');
    $subject = SubjectManager::get_by_id($subjectid);

    if (!$subject || $subject->planid != $planid) {
        throw new moodle_exception('invalidsubject', 'local_curriculum');
    }
}
$PAGE->set_title($title);
$PAGE->set_heading($title);

$form = new subject_form(null, 
                ['subject' => $subject,
                'areas' => $areas,
                'planid' => $planid
                ]
            );

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/curriculum/index.php'));
}

if ($data = $form->get_data()) {
    if (!empty($data->id)) {
        SubjectManager::update_all($data->id,
                    $data->subjectname,
                    $data->ihs,
                    $data->areaid
            );
    } else {
        SubjectManager::create_subject($data->planid, 
                        $data->subjectname,
                        $data->areaid,
                        $data->ihs
                    );
    }
    redirect(new moodle_url('/local/curriculum/index.php'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();