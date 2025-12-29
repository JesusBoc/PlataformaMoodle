<?php
namespace local_coursebuilder\infrastructure\course;

defined('MOODLE_INTERNAL') || die();

use context_coursecat;
use local_coursebuilder\domain\repository\CohortRepositoryInterface;
use local_coursebuilder\domain\model\CohortDTO;

class CohortRepository implements CohortRepositoryInterface{
    public function get_by_level(int $categoryid): array
    {
        global $DB;

        $context = context_coursecat::instance($categoryid);
        $contextid = $context->id;

        $result = [];

        $cohorts = $DB->get_records('cohort',
                        ['contextid' => $contextid]
        );

        foreach($cohorts as $cohort){
            $parts = explode(' - ', $cohort->name);
            if(count($parts) !== 2){
                continue;
            }

            [$name, $year] = $parts;

            if(!is_numeric($year)){
                continue;
            }

            $result[] = new CohortDTO(
                $cohort->id,
                trim($name),
                (int)$year,
                $cohort->name
            );
        }
        return $result;
    }
}