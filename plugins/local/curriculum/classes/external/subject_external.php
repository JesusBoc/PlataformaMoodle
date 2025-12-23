<?php
namespace local_curriculum\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use context_system;
use local_curriculum\service\SubjectManager;

class subject_external extends external_api {

    public static function delete_parameters() {
        return new external_function_parameters([
            'subjectid' => new external_value(PARAM_INT, 'Subject ID'),
        ]);
    }

    public static function delete($subjectid) {
        $params = self::validate_parameters(self::delete_parameters(), [
            'subjectid' => $subjectid,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/curriculum:manageplans', $context);

        SubjectManager::delete_by_id($params['subjectid']);

        return true;
    }

    public static function delete_returns() {
        return new external_value(PARAM_BOOL, 'Result');
    }
}
