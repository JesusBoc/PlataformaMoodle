<?php
namespace local_curriculum\output;

defined('MOODLE_INTERNAL') || die();

use local_curriculum\service\IndexService;
use core\output\renderable;
use core\output\templatable;
use core\output\renderer_base;

class index implements renderable, templatable {

    public function export_for_template(renderer_base $output): array {
        return [
            'categories' => IndexService::get_structure()
        ];
    }
}