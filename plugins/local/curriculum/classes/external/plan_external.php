<?php
namespace local_curriculum\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use context_system;
use local_curriculum\service\PlanEditorService;

class plan_external extends external_api {

    public static function save_structure_parameters() {
        return new external_function_parameters([
            'planid' => new external_value(PARAM_INT, 'Plan ID'),
            'areas' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Area ID', VALUE_OPTIONAL),
                    'sortorder' => new external_value(PARAM_INT, 'Order'),
                    'subjects' => new external_multiple_structure(
                        new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'Subject ID'),
                            'sortorder' => new external_value(PARAM_INT, 'Order'),
                        ])
                    )
                ])
            )
        ]);
    }

    public static function save_structure($planid, $areas) {
        $params = self::validate_parameters(
            self::save_structure_parameters(),
            ['planid' => $planid, 'areas' => $areas]
        );

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/curriculum:manageplans', $context);

        PlanEditorService::save_structure($params['planid'], $params['areas']);

        return true;
    }

    public static function save_structure_returns() {
        return new external_value(PARAM_BOOL, 'Result');
    }
}
