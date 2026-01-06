<?php

namespace local_academicload\service;

use core\exception\moodle_exception;
use core\plugininfo\enrol;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles enrolment of teachers into Moodle courses.
 */
class TeacherEnrolmentManager {

    /**
     * Enrol a user into a course with a given role.
     *
     * This operation is idempotent:
     * - If the user is already enrolled with the role, nothing happens.
     *
     * @param int $userid Moodle user ID
     * @param int $courseid Moodle course ID
     * @param int $roleid Moodle role ID (e.g. editingteacher)
     *
     * @throws \moodle_exception If enrolment cannot be completed
     */
    public function enrol(
        int $userid,
        int $courseid,
        int $roleid
    ): void {
        global $DB;

        if ($this->is_enrolled($userid, $courseid)) {
            return;
        }

        $enrol = enrol_get_plugin('manual');
        if (!$enrol){
            throw new moodle_exception(
                'manualenrolnotavaliable',
                'enrol_manual'
            );
        }

        $instance = $this->get_manual_instance($courseid);

        $enrol->enrol_user(
            $instance,
            $userid,
            $roleid,
            time()
        );
    }

    /**
     * Check if a user is already enrolled in a course.
     *
     * @param int $userid
     * @param int $courseid
     *
     * @return bool
     */
    public function is_enrolled(
        int $userid,
        int $courseid
    ): bool {
        global $DB;

        return $DB->record_exists(
            'user_enrolments',
            [
                'userid' => $userid,
                'enrolid' => $this->get_enrolid($courseid)
            ]
        );
    }

    private function get_manual_instance(int $courseid): \stdClass{
        global $DB;

        $instance = $DB->get_record(
            'enrol',
            [
                'courseid' => $courseid,
                'enrol' => 'manual'
            ]
        );
        
        if($instance){
            return $instance;
        }

        $enrol = enrol_get_plugin('manual');
        if (!$enrol){
            throw new moodle_exception(
                'manualenrolnotavaliable',
                'enrol_manual'
            );
        }

        $course = $DB->get_record(
            'course',
            ['id' => $courseid],
            '*',
            MUST_EXIST
        );

        $enrolid = $enrol->add_instance(
            $courseid,
            ['status' => ENROL_INSTANCE_ENABLED]
        );

        return $DB->get_record(
            'enrol',
            ['id' => $enrolid],
            '*',
            MUST_EXIST
        );
    }
    private function get_enrolid(int $courseid){
        global $DB;

        $instance = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol'    => 'manual'
        ], 'id');

        return $instance ? (int)$instance->id : null;
    }
}
