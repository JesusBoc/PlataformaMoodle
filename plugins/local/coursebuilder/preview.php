<?php
require_once('../../config.php');
require_login();

use local_coursebuilder\service\CourseBuildOrchestator;
use local_coursebuilder\infrastructure\curriculum\PlanRepository;
use local_coursebuilder\infrastructure\course\CohortRepository;
use local_coursebuilder\infrastructure\course\CourseRepository;
use local_coursebuilder\service\CourseCreationService;
use local_coursebuilder\service\CoursePreviewPresenter;

$categoryid = required_param('categoryid', PARAM_INT);

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$PAGE->set_url(new moodle_url('/local/coursebuilder/preview.php', ['categoryid' => $categoryid]));
$PAGE->set_context($context);

echo $OUTPUT->header();

$orchestator = new CourseBuildOrchestator(
    new PlanRepository(),
    new CourseRepository(),
    new CohortRepository()
);

$actions = $orchestator->preview($categoryid);
$creationService = new CourseCreationService();

$preview = new \local_coursebuilder\output\preview($creationService->build_preview($actions));

echo $OUTPUT->render($preview);

echo $OUTPUT->footer();
