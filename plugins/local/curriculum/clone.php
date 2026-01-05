<?php
require_once(__DIR__ . '/../../config.php');

require_login();

$categoryid = required_param('categoryid', PARAM_INT);

$context = context_system::instance();
require_capability('local/curriculum:manageplans', $context);

$PAGE->set_url('/local/curriculum/clone.php', ['categoryid' => $categoryid]);

use local_curriculum\service\PlanManager;

try {
    PlanManager::clone_from_active($categoryid);

    redirect(
        new moodle_url('/local/curriculum/index.php'),
        get_string('plancloned', 'local_curriculum'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );

} catch (Throwable $e) {
    redirect(
        new moodle_url('/local/curriculum/index.php'),
        get_string('plancloneerror', 'local_curriculum'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}
