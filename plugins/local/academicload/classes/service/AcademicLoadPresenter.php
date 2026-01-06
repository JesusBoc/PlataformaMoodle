<?php

namespace local_academicload\service;

use local_academicload\output\AcademicViewModel;
use local_academicload\output\AssignmentRowViewModel;
use local_academicload\output\CohortCardViewModel;
use local_academicload\repository\TeachingAssignmentRepository;
use local_academicload\repository\UserRepository;
use local_coursebuilder\infrastructure\course\CohortRepository;
use local_coursebuilder\infrastructure\curriculum\PlanRepository;

defined('MOODLE_INTERNAL') || die();

class AcademicLoadPresenter {
    public function __construct(
        private TeachingAssignmentRepository $assignmentRepo,
        private PlanRepository $planRepo,
        private CohortRepository $cohortRepo,
        private UserRepository $userRepo,
        private int $categoryID
    )
    {
        $plan = $this->planRepo->get_active_plan_by_category($this->categoryID);
        $cards = [];
        $cohorts = $this->cohortRepo->get_by_level($this->categoryID);
        foreach($cohorts as $cohort){
            $rows = [];

            foreach($plan->subjects as $subject){
                $assignment = $this->assignmentRepo->get_by_cohortid_subjectid(
                    $cohort->id,
                    $subject->id
                );

                $teachername = null;

                if($assignment){
                    $teachername = $this->userRepo->get_fullname(
                        $assignment->get_teacherid()
                    );
                }
                $rows[] = new AssignmentRowViewModel(
                    $subject->id,
                    $subject->name,
                    $assignment,
                    $teachername
                );
            }

            $cards[] = new CohortCardViewModel(
                $cohort->id,
                $cohort->name,
                $cohort->year,
                $rows
            );
        }
        return new AcademicViewModel($cards);
    }
}