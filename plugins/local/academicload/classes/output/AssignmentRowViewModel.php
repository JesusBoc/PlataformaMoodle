<?php

namespace local_academicload\output;

defined('MOODLE_INTERNAL') || die();

use local_academicload\domain\AssignmentStatus;
use local_academicload\domain\TeachingAssignment;

class AssignmentRowViewModel {

    public int $subjectid;
    public string $subjectname;

    public ?int $teacherid;
    public ?string $teachername;

    public string $status;
    public string $statusbadge;
    public string $statuslabel;

    public bool $can_assign;
    public bool $can_retry;

    public function __construct(
        int $subjectid,
        string $subjectname,
        ?TeachingAssignment $assignment,
        ?string $teachername
    ) {
        $this->subjectid = $subjectid;
        $this->subjectname = $subjectname;

        $this->teacherid = $assignment?->get_teacherid();
        $this->teachername = $teachername;

        $this->status = $assignment?->get_status() ?? AssignmentStatus::PENDING;

        $this->map_status();
    }

    private function map_status(): void{
        switch ($this->status) {
            case AssignmentStatus::APPLIED:
                $this->statusbadge = 'success';
                $this->statuslabel = get_string('statusapplied', 'local_academicload');
                $this->can_assign = true;
                $this->can_retry = false;
                break;

            case AssignmentStatus::ERROR:
                $this->statusbadge = 'danger';
                $this->statuslabel = get_string('statuserror', 'local_academicload');
                $this->can_assign = false;
                $this->can_retry = true;
                break;
            
            case AssignmentStatus::NO_COURSE:
                $this->statusbadge = 'warning';
                $this->statuslabel = get_string('statusnocourse', 'local_academicload');
                $this->can_assign = true;
                $this->can_retry = true;
                break;

            default:
                $this->statusbadge = 'warning';
                $this->statuslabel = get_string('statuspending', 'local_academicload');
                $this->can_assign = true;
                $this->can_retry = false;
        }
    }
}