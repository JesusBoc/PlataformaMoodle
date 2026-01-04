<?php
namespace local_coursebuilder\service;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\CourseAction;

class CoursePreviewPresenter {
    /** @var array<int, array{action: string, subject: string, cohort: string, year: int, shortname: string, fullname: string, exists: bool}[]> $actionmap */
    public array $actionmap;

    public int $cohortcount;
    public int $createcount;

    /**
     * @param array<int, array{action: string, subject: string, cohort: string, year: int, shortname: string, fullname: string, exists: bool}[]> $actionmap
     */
    public function __construct(array $actionmap) {
        $this->actionmap = $actionmap;

        $this->cohortcount = count($actionmap);
        $this->createcount = 0;

        foreach ($actionmap as $actions) {
            foreach ($actions as $action) {
                if ($action['action'] === CourseAction::CREATE) {
                    $this->createcount++;
                }
            }
        }
    }

    public function get_structure(): array{
        $cohorts = [];

        foreach($this->actionmap as $actionlist){
            if (empty($actionlist)) {
                continue;
            }
            $cohorts[] = [
                'name' => $actionlist[0]['cohort'],
                'year' => $actionlist[0]['year'],
                'actions' => $this->action_to_UI($actionlist)
            ];
        }
        $out = [
            'cohorts' => $cohorts,
            'cohortcount' => $this->cohortcount,
            'createcount' => $this->createcount,
        ];
        return $out;
    }

    /**
     * @param array{action: string, subject: string, cohort: string, year: int, shortname: string, fullname: string, exists: bool}[] $actions
     * @return array{type: string, icon: string, badge: string, label: string, fullname: string, shortname: string}[]
     */
    private function action_to_UI(array $actions): array{
        $out = [];
        foreach($actions as $action){
            $out[] = match ($action['action']) {
                CourseAction::CREATE => [
                    'type' => 'create',
                    'icon' => 'i/add',
                    'badge' => 'success',
                    'label' => 'Se creará',
                    'fullname' => $action['fullname'],
                    'shortname' => $action['shortname'],
                ],
                CourseAction::KEEP => [
                    'type' => 'keep',
                    'icon' => 'i/checked',
                    'badge' => 'secondary',
                    'label' => 'Ya existe',
                    'fullname' => $action['fullname'],
                    'shortname' => $action['shortname'],
                ],
                default => [
                    'type' => 'error',
                    'icon' => 'i/warning',
                    'badge' => 'danger',
                    'label' => 'Error',
                    'fullname' => '',
                    'shortname' => '',
                ],
            };
        }
        return $out;
    }
}