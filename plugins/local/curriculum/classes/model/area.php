<?php

namespace local_curriculum\model;

defined('MOODLE_INTERNAL') || die();

abstract class area extends academic_entity{

    protected static string $table = 'local_curriculum_plan_areas';
    
}