<?php
namespace local_coursebuilder\domain\repository;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\ExistingCourseDTO;

interface CourseRepositoryInterface {
    /**
     * @return ExistingCourseDTO[]
     */
    public function get_by_level(int $categoryid): array;
}