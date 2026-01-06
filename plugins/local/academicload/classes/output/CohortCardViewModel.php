<?php

namespace local_academicload\output;

use local_academicload\domain\AssignmentStatus;

defined('MOODLE_INTERNAL') || die();
class CohortCardViewModel {

    public int $cohortid;
    public string $name;
    public int $year;

    /** @var AssignmetRowViewModel[] */
    public array $assignments;

    public int $total;
    public int $applied;
    public int $pending;
    public int $error;

    /**
     * @param AssignmetRowViewModel[] $assignments
     */
    public function __construct(
        int $cohortid,
        string $name,
        int $year,
        array $assignments
    )
    {
        
        $this->cohortid = $cohortid;
        $this->name = $name;
        $this->year = $year;
        $this->assignments = $assignments;
        $this->calculate();
    }

    private function calculate(): void{
        $this->total = count($this->assignments);
        $this->applied = 0;
        $this->pending = 0;
        $this->error = 0;

        foreach($this->assignments as $a){
            match($a->status) {
                AssignmentStatus::APPLIED => $this->applied++,
                AssignmentStatus::ERROR => $this->error++,
                default => $this->pending++,
            };
        }
    }
}