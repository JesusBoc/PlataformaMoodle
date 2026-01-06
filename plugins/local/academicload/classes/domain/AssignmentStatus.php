<?php

namespace local_academicload\domain;

defined('MOODLE_INTERNAL') || die();

final class AssignmentStatus {

    public const PENDING = 'pending';
    public const APPLIED = 'applied';
    public const ERROR = 'error';

    public static function is_valid(string $status): bool{
        return in_array($status, [
            self::PENDING,
            self::APPLIED,
            self::ERROR,
        ], true);
    }
}