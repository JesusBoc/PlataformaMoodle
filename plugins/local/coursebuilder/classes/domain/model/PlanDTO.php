<?php
namespace local_coursebuilder\domain\model;

defined('MOODLE_INTERNAL') || die();

class PlanDTO {
    /** @param SubjectDTO[] $subjects*/
    public function __construct(
        public int $planid,
        public int $categoryid,
        public string $name,
        public array $subjects
    ) {}
}