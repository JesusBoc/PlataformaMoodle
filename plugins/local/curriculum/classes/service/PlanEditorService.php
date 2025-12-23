<?php

namespace local_curriculum\service;

use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();
class PlanEditorService {

    public static function get_structure(int $planid): array {
        $plan = PlanManager::get_by_id($planid);

        $areas = AreaManager::get_by_plan($planid);

        $structure = [];

        foreach ($areas as $area) {
            $subjects = SubjectManager::get_by_area($area->id);

            $structure[] = [
                'id' => $area->id,
                'name' => $area->areaname,
                'sortorder' => $area->sortorder,
                'is_virtual' => false,
                'subjects' => self::map_subjects($subjects),
                'editurl' => (new \moodle_url(
                    '/local/curriculum/area.php',
                    ['planid' => $area->planid,
                    'id' => $area->id]
                )),
                'addurl' => (new \moodle_url(
                    '/local/curriculum/subject.php',
                    ['planid' => $area->planid,
                    'areaid' => $area->id]
                ))
            ];
        }
        $subjects = SubjectManager::get_outside_area($planid); //Asignaturas sin area en el plan
        $structure[] = [
            'id' => null,
            'name' => get_string('noarea','local_curriculum'),
            'sortorder' => PHP_INT_MAX,
            'is_virtual' => true,
            'subjects' => self::map_subjects($subjects),
            'addurl' => (new \moodle_url(
                    '/local/curriculum/subject.php',
                    ['planid' => $planid]
                ))
        ];
        return [
            'plan' => $plan,
            'areas' => $structure,
            'readonly' => $plan->active == 0
        ];
    }
    public static function save_structure(int $planid, array $areas): void {
        PlanManager::transactional(
            function() use ($planid, $areas){
                //Trata de encontrar el plan, arroja excepcion
                //si no lo hace
                $_ = PlanManager::get_by_id($planid);

                if(!isset($areas) || !is_array($areas)){
                    throw new moodle_exception('invalidstructure',
                                    'local_curriculum');
                }

                foreach($areas as $area){
                    if($area['id']!==null){
                        $areaplanid = AreaManager::get_by_id($area['id'])->planid;
                        if($planid != $areaplanid){
                            throw new moodle_exception('areanotinplan',
                                            'local_curriculum');
                        }
                        AreaManager::update_order($area['id'],$area['sortorder']);
                    }
                    foreach($area['subjects'] as $subject){
                        $subjectplanid = SubjectManager::get_by_id($subject['id'])->planid;
                        if($planid != $subjectplanid){
                            throw new moodle_exception('subjectnotinplan',
                                            'local_curriculum');
                        }
                        SubjectManager::update_order($subject['id'], (int)$subject['sortorder']);
                    }
                }
            }
        );
    }

    private static function map_subjects(array $subjects): array {
        $result = [];

        foreach ($subjects as $s) {
            $result[] = [
                'id' => $s->id,
                'name' => $s->subjectname,
                'ihs' => $s->ihs,
                'sortorder' => $s->sortorder,
                'areaid' => $s->areaid ?? null,
                'editurl' => (new \moodle_url(
                    '/local/curriculum/subject.php',
                    ['planid' => $s->planid,
                    'id' => $s->id]
                ))
            ];
        }
        return $result;
    }
}