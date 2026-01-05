<?php
namespace local_coursebuilder\domain\model;

defined('MOODLE_INTERNAL') || die();

class CourseAction{
    const CREATE = 'create';
    const KEEP = 'keep';

    public function __construct(
        public string $action,
        public SubjectDTO $subject,
        public string $cohortname,
        public int $year,
        public ?ExistingCourseDTO $existing = null
    ){}
}