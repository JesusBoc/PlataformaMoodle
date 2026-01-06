<?php
namespace local_academicload\output;

defined('MOODLE_INTERNAL') || die();

class AcademicViewModel {
    /** @var CohortCardViewModel[] */
    public array $cohorts;

    /**
     * @param CohortCardViewModel[] $cohorts
     */
    public function __construct(array $cohorts)
    {
        $this->cohorts = $cohorts;
    }
}