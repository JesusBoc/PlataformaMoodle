<?php
namespace local_academicload\observer;

defined('MOODLE_INTERNAL') || die();

use core\event\course_created;
use core_analytics\course;
use local_academicload\repository\TeachingAssignmentRepository;
use local_academicload\service\AcademicloadService;
use local_academicload\service\CourseResolutionService;
use local_academicload\service\TeacherEnrolmentManager;

final class CourseCreatedObserver {
    public static function handle(course_created $event): void {
        global $DB;

        $courseid = $event->objectid;

        $course = $DB->get_record(
            'course',
            ['id' => $courseid],
            '*',
            IGNORE_MISSING
        );
        if(!$course){
            return;
        }
        $service = static::build_service();

        $service->retry_pending();
    }
    private static function build_service(): AcademicloadService{
            return new AcademicloadService(
                new TeachingAssignmentRepository,
                new CourseResolutionService,
                new TeacherEnrolmentManager
            );
        }
}