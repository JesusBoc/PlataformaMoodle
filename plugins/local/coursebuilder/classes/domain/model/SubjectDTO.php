<?php
namespace local_coursebuilder\domain\model;

defined('MOODLE_INTERNAL') || die();

class SubjectDTO {
    public function __construct(
        public int $id,
        public string $name,
        public int $ihs
    ) {}
}