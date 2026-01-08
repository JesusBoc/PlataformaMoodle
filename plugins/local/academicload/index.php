<?php

use local_academicload\form\SelectLevelForm;

require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);
$form = new SelectLevelForm();

if ($form->is_cancelled()) {
    redirect(new moodle_url('/'));
}

if ($data = $form->get_data()) {
    redirect(new moodle_url('/local/academicload/view.php', [
        'categoryid' => $data->categoryid
    ]));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();