<?php
namespace local_coursebuilder\domain\model;

defined('MOODLE_INTERNAL') || die();

class PlanDTO {
    public function __construct(
        public int $planid,
        public int $categoryid,
        public string $name,
        /** @var SubjectDTO[] */
        public array $subjects
    ) {}
}