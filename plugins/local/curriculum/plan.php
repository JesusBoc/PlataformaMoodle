<?php

require_once('../../config.php');

use local_curriculum\form\plan_form;
use local_curriculum\service\PlanManager;

$categoryid = required_param('categoryid', PARAM_INT);
$planid = optional_param('id', 0, PARAM_INT);  // Obtener el ID del plan (si lo hay)

$plan = $planid ? PlanManager::get_by_id($planid) : null;

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