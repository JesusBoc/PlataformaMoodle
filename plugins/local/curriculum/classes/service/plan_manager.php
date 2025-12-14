<?php

namespace local_curriculum\service;

use local_curriculum\model\plan;
use local_curriculum\model\area;
use local_curriculum\model\subject;

defined('MOODLE_INTERNAL') || die();

class plan_manager {
    public static function create_plan(int $categoryid, string $name): ?int{
        $name = trim($name);
        if($name=''){
            return null;
        }
        return plan::create([
            'categoryid' => $categoryid,
            'name' => $name,
            'version' => 1,
            'active' => 0
        ]);
    }
    public static function get_active_by_category(int $categoryid): ?object{
        return plan::get_one_by(['categoryid' => $categoryid,
                                'active' => 1]
                            );
    }

    public static function get_by_id(int $planid) {
        return plan::get_by_id($planid);
    }

    public static function activate_plan(int $planid): void {
        $validation = self::validate_plan($planid);
        if(!$validation['valid']){
            return;
        }
        $plan = plan::get_by_id($planid);
        $categoryid = $plan->categoryid;
        self::deactivate_all($categoryid);
        plan::update($planid, ['active' => 1]);
    }

    public static function deactivate_plan(int $planid): void {
        plan::update($planid, ['active' => 0]);
    }

    public static function clone_from_active(int $categoryid): int {
        $plan = self::get_active_by_category($categoryid);
        $planid = $plan -> id;
        $newversion = $plan -> version + 1;
        $areas = area::get_by_plan($planid);
        $subjects = subject::get_by_plan($planid);
        $clonedplan = plan::create([
            'categoryid' => $categoryid,
            'name' => $plan -> name,
            'version' => $newversion,
            'active' => 1,
        ]);
        $areaMap = [];
        foreach ($areas as $area) {
            $newareaid = area::create([
                'planid'     => $clonedplan,
                'areaname'   => $area -> areaname,
                'sortorder'  => $area -> sortorder
            ]);
            $areaMap[$area->id] = $newareaid;
        }
        foreach ($subjects as $subject) {
            subject::create([
                'planid'     => $clonedplan,
                'subjectname'   => $subject -> subjectname,
                'areaid'   => $subject->areaid ? $areaMap[$subject->areaid] : null,
                'ihs'   => $subject -> ihs,
                'sortorder'  => $subject -> sortorder
            ]);
        }
        self::deactivate_plan($planid);
        return $clonedplan;
    }

    public static function validate_plan(int $planid): array {
        // áreas sin asignaturas
        // planes inválidos
        $areas = area::get_by_plan($planid);

        $emptyareas = 0;
        $errors = [];
        $totalihs = 0;
        foreach($areas as $area){
            $hassubjects = subject::get_many_by(['areaid' => $area -> id]);
            if(empty($hassubjects)){
                $emptyareas += 1;
                $errors[] = "{$area->areaname} is empty!";
            }
        }
        $subjects = subject::get_by_plan($planid);
        foreach($subjects as $subject){
            $totalihs += $subject->ihs;
        }
        return [
            'valid' => $emptyareas==0,
            'total_ihs' => $totalihs,
            'errors' => $errors
        ];
    }

    private static function deactivate_all(int $categoryid): void {
        plan::set('active', 0, ['categoryid' => $categoryid]);
    }

    /**
     * Obtiene las categorías de cursos (niveles académicos).
     *
     * @return array
     */
    public static function get_categories(): array {
        global $DB;

        return $DB->get_records(
            'course_categories',
            ['visible' => 1],
            'sortorder ASC',
            'id, name'
        );
    }
}
