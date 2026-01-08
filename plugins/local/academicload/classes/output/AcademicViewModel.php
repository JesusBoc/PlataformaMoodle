<?php
namespace local_academicload\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;

defined('MOODLE_INTERNAL') || die();

class AcademicViewModel implements renderable, templatable{
    /** @var CohortCardViewModel[] */
    public array $cohorts;

    /**
     * @param CohortCardViewModel[] $cohorts
     */
    public function __construct(array $cohorts)
    {
        $this->cohorts = $cohorts;
    }

    public function export_for_template(renderer_base $output)
    {
        $result = [];
        foreach($this->cohorts as $cohort){
            $assignments = [];
            foreach($cohort->assignments as $assignment){
                $assignments[] = [
                    'subjectid' => $assignment->subjectid,
                    'subjectname' => $assignment->subjectname,
                    'teacherid' => $assignment->teacherid,
                    'teachername' => $assignment->teachername,
                    'status' => $assignment->status,
                    'statuslabel' => $assignment->statuslabel,
                    'statusbadge' => $assignment->statusbadge,
                    'canassign' => $assignment->can_assign,
                    'canretry' => $assignment->can_retry,
                ];
            }
            $result[] = [
                'cohortid' => $cohort->cohortid,
                'name' => $cohort->name,
                'year' => $cohort->year,
                'assignments' => $assignments,
                'total' => $cohort->total,
                'applied' => $cohort->applied,
                'pending' => $cohort->pending,
                'error' => $cohort->error,
            ];
        }
        return [
            'cohorts' => $result
        ];
    }
}