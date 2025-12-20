<?php
require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/curriculum/index.php'));
$PAGE->set_title(get_string('avaliableplans', 'local_curriculum'));
$PAGE->set_heading(get_string('manageplans', 'local_curriculum'));

echo $OUTPUT->header();

$editor = new \local_curriculum\output\index();
echo $OUTPUT->render($editor);

echo $OUTPUT->footer();
