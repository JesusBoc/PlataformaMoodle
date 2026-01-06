<?php

namespace local_academicload\service;

use Aws\RetryMiddleware;
use local_academicload\domain\AssignmentStatus;
use local_academicload\domain\TeachingAssignment;
use local_academicload\repository\TeachingAssignmentRepository;
use local_academicload\service\CourseResolutionService;
use local_academicload\service\TeacherEnrolmentManager;

defined('MOODLE_INTERNAL') || die();

class AcademicloadService {
    public function __construct(
        private TeachingAssignmentRepository $repository,
        private CourseResolutionService $courseResolver,
        private TeacherEnrolmentManager $enrolmentManager
    ){}

    public function assign_teacher(
        int $teacherid,
        int $subjectid,
        int $cohortid,
        int $roleid,
    ): TeachingAssignment {

        $existing = $this->repository->get_unique(
            $teacherid,
            $subjectid,
            $cohortid
        );

        if($existing) {
            return $existing;
        }

        $assignment = TeachingAssignment::create(
            $teacherid,
            $subjectid,
            $cohortid,
            $roleid,
        );

        $this->repository->insert($assignment);

        return $assignment;
    }

    public function apply(
        TeachingAssignment $assignment
    ): void {
        if($assignment->get_status() === AssignmentStatus::APPLIED){
            return;
        }
        $course = $this->courseResolver->resolve(
            $assignment->get_subjectid(),
            $assignment->get_cohortid()
        );

        if(!$course){
            return;
        }

        $courseid = $course->id;

        try{
            $this->enrolmentManager->enrol(
                $assignment->get_teacherid(),
                $courseid,
                $assignment->get_roleid()
            );
            $this->repository->mark_applied(
                $assignment->get_id(),
                $courseid
            );

        } catch (\Throwable $e){
            $this->repository->mark_error(
                $assignment->get_id(),
                $e->getMessage()
            );
        }
    }

    public function retry_pending():void {
        $assignments = $this->repository->get_retryable();

        if(!$assignments){
            return;
        }

        foreach ($assignments as $assignment){
            $this->apply($assignment);
        }
    }
}