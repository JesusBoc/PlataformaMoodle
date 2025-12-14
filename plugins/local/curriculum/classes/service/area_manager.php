<?php

namespace local_curriculum\service;

use local_curriculum\model\area;
use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

abstract class area_manager {
    public static function create_area(int $planid, string $name, int $sortorder = null) : int {
        if(!plan_manager::is_active($planid)){
            throw new moodle_exception('planisnotactive','local_curriculum');
        }

        $name = trim($name);

        if ($name == ''){
            throw new moodle_exception('emptysubjectname','local_curriculum');
        }
        
    }
}