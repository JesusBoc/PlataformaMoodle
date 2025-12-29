<?php
namespace local_coursebuilder\infrastructure\curriculum;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\repository\PlanRepositoryInterface;
use local_coursebuilder\domain\model\PlanDTO;
use local_coursebuilder\domain\model\SubjectDTO;
use local_coursebuilder\domain\exception\NoActivePlanException;

use local_curriculum\service\PlanManager;
use local_curriculum\service\SubjectManager;

class PlanRepository implements PlanRepositoryInterface {

    public function get_active_plan_by_category(int $categoryid): PlanDTO {
        $plan = PlanManager::get_active_by_category($categoryid);

        if (!$plan) {
            throw new NoActivePlanException('noactiveplan', 'local_coursebuilder', '', $categoryid);
        }

        $subjects = [];
        foreach (SubjectManager::get_by_plan($plan->id) as $subject) {
            $subjects[] = new SubjectDTO(
                $subject->id,
                $subject->subjectname,
                $subject->ihs
            );
        }

        return new PlanDTO(
            $plan->id,
            $plan->categoryid,
            $plan->name,
            $subjects
        );
    }
}