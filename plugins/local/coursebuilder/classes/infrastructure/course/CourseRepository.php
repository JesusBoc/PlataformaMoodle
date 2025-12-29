<?php
namespace local_coursebuilder\infrastructure\course;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\ExistingCourseDTO;
use local_coursebuilder\domain\repository\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface {
    public function get_by_level(int $categoryid): array
    {
        global $DB;

        $courses = $DB->get_records('course',
                        ['category' => $categoryid]
        );
        $result = [];

        foreach($courses as $course) {
            if(empty($course->fullname)){
                continue;
            }

            $parts = explode(' - ', $course->fullname);
            if(count($parts) !== 3){
                continue;
            }

            [$subject,
            $cohort,
            $year] = $parts;

            if(!is_numeric($year)){
                continue;
            }

            $result[] = new ExistingCourseDTO(
                $course->id,
                $course->fullname,
                $course->shortname,
                trim($subject),
                trim($cohort),
                (int)$year
            );
        }
        return $result; 
    }
}