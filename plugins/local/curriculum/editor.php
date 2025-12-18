<?php
require_once('../../config.php');
require_login();

$planid = required_param('planid', PARAM_INT);

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$PAGE->set_url(new moodle_url('/local/curriculum/editor.php', ['planid' => $planid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('planeditor', 'local_curriculum'));
$PAGE->set_heading(get_string('planeditor', 'local_curriculum'));

echo $OUTPUT->header();

$editor = new \local_curriculum\output\plan_editor($planid);
echo $OUTPUT->render($editor);

echo $OUTPUT->footer();
