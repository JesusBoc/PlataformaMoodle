<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_created',
        'callback'  => '\local_academicload\observer\CourseCreatedObvserver::handle',
        'priority'  => 9999,
    ],
];
