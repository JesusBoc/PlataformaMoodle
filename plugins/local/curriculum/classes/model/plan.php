<?php

namespace local_curriculum\model;

defined('MOODLE_INTERNAL') || die();

abstract class plan extends curriculum_entity {

    protected static string $table = 'local_curriculum_plan';

}