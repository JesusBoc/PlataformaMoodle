<?php
namespace local_coursebuilder\domain\model;

defined('MOODLE_INTERNAL') || die();

class ExistingCourseDTO {
    public function __construct(
        public int $courseid,
        public string $fullname,
        public string $shortname,
        public string $subjectname,
        public string $cohortname,
        public int $year
    ) {}
}