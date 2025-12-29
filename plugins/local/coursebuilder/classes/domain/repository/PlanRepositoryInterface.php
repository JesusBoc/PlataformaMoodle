<?php
namespace local_coursebuilder\domain\repository;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\PlanDTO;

interface PlanRepositoryInterface {
    /**
     * @throws \local_coursebuilder\domain\exception\NoActivePlanException
     */
    public function get_active_plan_by_category(int $categoryid): PlanDTO;
}