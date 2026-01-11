<?php

use local_academicload\output\Renderer;
use local_academicload\repository\TeachingAssignmentRepository;
use local_academicload\repository\UserRepository;
use local_academicload\service\AcademicLoadBuilder;
use local_coursebuilder\infrastructure\course\CohortRepository;
use local_coursebuilder\infrastructure\curriculum\PlanRepository;

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/academicload:manageload', $context);

$categoryid = required_param('categoryid', PARAM_INT);

$PAGE->set_url(
    new moodle_url('/local/academicload/view.php',
    ['categoryid' => $categoryid])
);
$PAGE->set_context($context);
$PAGE->set_title(
    get_string(
        'pluginname',
        'local_academicload'
    )
);
$PAGE->set_heading(
    get_string(
        'pluginname',
        'local_academicload'
    )
);

echo $OUTPUT->header();

$builder = new AcademicLoadBuilder(
    new TeachingAssignmentRepository(),
    new PlanRepository(),
    new CohortRepository(),
    new UserRepository(),
    $categoryid
);

$viewmodel = $builder->buildViewModel();

echo $OUTPUT->render($viewmodel);

echo $OUTPUT->footer();