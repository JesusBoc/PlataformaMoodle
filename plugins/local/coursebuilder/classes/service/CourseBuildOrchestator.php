<?php
namespace local_coursebuilder\service;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\CourseAction;
use local_coursebuilder\domain\repository\CohortRepositoryInterface;
use local_coursebuilder\domain\repository\PlanRepositoryInterface;
use local_coursebuilder\domain\repository\CourseRepositoryInterface;

class CourseBuildOrchestator{

    public function __construct(
        private PlanRepositoryInterface $planRepo,
        private CourseRepositoryInterface $courseRepo,
        private CohortRepositoryInterface $cohortRepo
    )
    {}

    /**
     * @return CourseAction[]
     */
    public function preview(int $categoryid): array{

        $plan = $this->planRepo->get_active_plan_by_category($categoryid);
        $existing = $this->courseRepo->get_by_level($categoryid);
        $cohorts = $this->cohortRepo->get_by_level($categoryid);

        $existingMap = [];
        foreach($existing as $course){
            if(!isset($existingMap[$course->cohortname])){
                $existingMap[$course->cohortname] = [];
            }
            $existingMap[$course->cohortname][] = $course;
        }

        $actions = [];
        foreach($cohorts as $cohort){
            $actions[$cohort->id] = [];
            foreach($plan->subjects as $subject){
                if(!isset($existingMap[$cohort->name])){
                    $actions[$cohort->id][] = new CourseAction(
                        CourseAction::CREATE,
                        $subject,
                        $cohort->name,
                        $cohort->year
                    );
                    continue;
                }
                $courseExists = false;
                foreach($existingMap[$cohort->name] as $course){
                    if($course->subjectname === $subject->name){
                        $courseExists = true;
                        $actions[$cohort->id][] = new CourseAction(
                            CourseAction::KEEP,
                            $subject,
                            $cohort->name,
                            $cohort->year,
                            $course
                        );
                        break;
                    }
                }
                if(!$courseExists){
                    $actions[$cohort->id][] = new CourseAction(
                        CourseAction::CREATE,
                        $subject,
                        $cohort->name,
                        $cohort->year
                    );
                }
            }
        }
        return $actions;
    }
}