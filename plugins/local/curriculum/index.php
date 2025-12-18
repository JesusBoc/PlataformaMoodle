<?php
require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/curriculum/index.php'));
$PAGE->set_title(get_string('curriculum', 'local_curriculum'));
$PAGE->set_heading(get_string('curriculum', 'local_curriculum'));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageplans', 'local_curriculum'));

use core\output\html_writer;
use local_curriculum\service\PlanManager;

$categories = PlanManager::get_categories();

if (empty($categories)){
    echo $OUTPUT -> notification('No hay categorías disponibles.','warning');
} else{
    echo html_writer::start_tag('ul');

    foreach($categories as $category){
        $url = new moodle_url(
            'plan.php',
            ['categoryid' => $category->id] 
        );
        echo html_writer::tag(
            'li',
            html_writer::link($url, format_string($category->name))
        );
    }

    echo html_writer::end_tag('ul');
}

echo $OUTPUT->footer();
