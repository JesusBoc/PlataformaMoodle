<?php
namespace local_curriculum\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use context_system;
use local_curriculum\service\AreaManager;

class area_external extends external_api {

    public static function delete_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Area ID'),
        ]);
    }

    public static function delete($areaid) {
        $params = self::validate_parameters(self::delete_parameters(), [
            'id' => $areaid,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/curriculum:manageplans', $context);

        AreaManager::delete_area($params['id']);

        return true;
    }

    public static function delete_returns() {
        return new external_value(PARAM_BOOL, 'Result');
    }
}
