<?php

namespace local_academicload\service;

use local_coursebuilder\api\CourseNamingApi;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class CourseResolutionService {
    public function resolve(
        int $subjectid,
        int $cohortid,
    ): ?stdClass{

        global $DB;
        if(!class_exists(CourseNamingApi::class)){
            return null;
        }

        $shortname = CourseNamingApi::build_shortname(
            $subjectid,
            $cohortid
        );
        return $DB->get_record('course', [
            'shortname' => $shortname
        ]) ?: null;
    }
}