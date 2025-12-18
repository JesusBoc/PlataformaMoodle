<?php
namespace local_curriculum\output;

defined('MOODLE_INTERNAL') || die();

use local_curriculum\service\PlanEditorService;
use core\output\renderable;
use core\output\templatable;
use core\output\renderer_base;

class plan_editor implements renderable, templatable {

    private int $planid;

    public function __construct(int $planid) {
        $this->planid = $planid;
    }

    public function export_for_template(renderer_base $output): array {
        return PlanEditorService::get_structure($this->planid);
    }
}