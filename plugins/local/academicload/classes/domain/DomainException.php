<?php

namespace local_academicload\domain;

defined('MOODLE_INTERNAL') || die();

use core\exception\moodle_exception;

class DomainException extends moodle_exception {
    public function __construct($errorcode, $link = '', $a = null, $debuginfo = null)
    {
        return parent::__construct(
            $errorcode,
            'local_academicload',
            $link,
            $a,
            $debuginfo
        );
    }
}