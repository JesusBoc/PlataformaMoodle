<?php

namespace local_curriculum\model;

defined('MOODLE_INTERNAL') || die();

class subject extends academic_entity {

    protected static string $table = 'local_curriculum_plan_subjects';

    function get_active_plan(int $categoryid) {
        
    }
}
