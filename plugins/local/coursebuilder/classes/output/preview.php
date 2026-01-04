<?php
namespace local_coursebuilder\output;

defined('MOODLE_INTERNAL') || die();

use core\output\renderable;
use core\output\templatable;
use core\output\renderer_base;
use local_coursebuilder\service\CoursePreviewPresenter;

class preview implements renderable, templatable {

    private CoursePreviewPresenter $presenter;

    public function __construct(array $viewmodel) {
        $this->presenter = new CoursePreviewPresenter($viewmodel);
    }

    public function export_for_template(renderer_base $output): array {
        return $this->presenter->get_structure();
    }
}