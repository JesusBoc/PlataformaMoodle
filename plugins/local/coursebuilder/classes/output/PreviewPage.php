<?php
namespace local_coursebuilder\output;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\CourseAction;

class PreviewPage {
    /** @var array<int, CourseAction[]> */
    public array $actionmap;

    public int $cohortcount;
    public int $createcount;

    public function __construct(array $actionmap) {
        $this->actionmap = $actionmap;

        $this->cohortcount = count($actionmap);
        $this->createcount = 0;

        foreach ($actionmap as $actions) {
            foreach ($actions as $action) {
                if ($action->action === CourseAction::CREATE) {
                    $this->createcount++;
                }
            }
        }
    }
}