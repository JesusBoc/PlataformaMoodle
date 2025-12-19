<?php

namespace local_curriculum\service;

use local_curriculum\model\plan;
use local_curriculum\model\area;
use local_curriculum\model\subject;
use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

abstract class PlanManager {
    public static function create_plan(int $categoryid, string $name): ?int{
        \core_course_category::get($categoryid, MUST_EXIST);
        $name = trim($name);
        if($name=''){
            throw new moodle_exception('emptyname',
                                'local_curriculum');
        }
        return plan::create([
            'categoryid' => $categoryid,
            'name' => $name,
            'version' => 1,
            'active' => 0
        ]);
    }
    public static function update_plan(int $planid, string $name): bool{
        return plan::update($planid,[
            'name' => $name
        ]);
    }
    public static function get_active_by_category(int $categoryid): ?object{
        return plan::get_one_by(['categoryid' => $categoryid,
                                'active' => 1]
                            );
    }
    public static function get_by_category(int $categoryid): ?array{
        return plan::get_many_by(['categoryid' => $categoryid]);
    }

    public static function get_by_id(int $planid): ?object {
        return plan::get_by_id($planid);
    }

    public static function is_active(int $planid): bool{
        $plan = self::get_by_id($planid);
        return $plan->active == 1;
    }

    public static function activate_plan(int $planid): void {
        try {
            $plan = plan::get_by_id($planid);
        } catch (\Throwable $th) {
            throw new moodle_exception('plan_not_found',
                            'local_curriculum');
        }
        $validation = self::validate_plan($planid);
        if(!$validation['valid']){
            return;
        }
        $categoryid = $plan->categoryid;
        self::deactivate_all($categoryid);
        plan::update($planid, ['active' => 1]);
    }

    public static function deactivate_plan(int $planid): void {
        plan::update($planid, ['active' => 0]);
    }

    public static function clone_from_active(int $categoryid): ?int {
        return plan::transactional(function() use ($categoryid) {
            $plan = self::get_active_by_category($categoryid);
            if (!$plan) {
                return null;
            }

            $clonedplan = plan::create([
                'categoryid' => $categoryid,
                'name' => $plan->name,
                'version' => $plan->version + 1,
                'active' => 1,
            ]);

            self::clone_areas_and_subjects($plan->id, $clonedplan);

            self::deactivate_plan($plan->id);

            return $clonedplan;
        });
    }

    private static function clone_areas_and_subjects(int $planid, int $clonedplan): void {
        $areas = area::get_by_plan($planid);
        $subjects = subject::get_by_plan($planid);

        $areaMap = [];
        foreach ($areas as $area) {
            $newareaid = area::create([
                'planid' => $clonedplan,
                'areaname' => $area->areaname,
                'sortorder' => $area->sortorder
            ]);
            $areaMap[$area->id] = $newareaid;
        }

        foreach ($subjects as $subject) {
            subject::create([
                'planid' => $clonedplan,
                'subjectname' => $subject->subjectname,
                'areaid' => $subject->areaid && isset($areaMap[$subject->areaid]) ? $areaMap[$subject->areaid] : null,
                'ihs' => $subject->ihs,
                'sortorder' => $subject->sortorder
            ]);
        }
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

    public static function transactional(callable $callback){
        return plan::transactional($callback);
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
