<?php

require_once('../../config.php');
require_login();

use local_curriculum\form\plan_form;
use local_curriculum\service\PlanManager;

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$PAGE->set_context($context);

$categoryid = required_param('categoryid', PARAM_INT);
$planid = optional_param('id', 0, PARAM_INT);  // Obtener el ID del plan (si lo hay)
$plan = null;

$PAGE->set_url('/local/curriculum/plan.php', ['categoryid' => $categoryid]);
$PAGE->set_context($context);

if($planid){
    $PAGE->set_title(get_string('editplan', 'local_curriculum'));
    $PAGE->set_heading(get_string('editplan', 'local_curriculum'));
    $plan = PlanManager::get_by_id($planid);
}

$PAGE->set_title(get_string('createplan', 'local_curriculum'));
$PAGE->set_heading(get_string('createplan', 'local_curriculum'));


$form = new plan_form(null, ['plan' => $plan,
                'categoryid' => $categoryid]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/curriculum/index.php'));
}

if ($data = $form->get_data()) {
    if (!empty($data->id)) {
        PlanManager::update_plan($data->id, $data->name);
    } else {
        PlanManager::create_plan($data->categoryid, $data->name);
    }
    redirect(new moodle_url('/local/curriculum/index.php'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();