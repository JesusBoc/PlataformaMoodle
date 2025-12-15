<?php

namespace local_curriculum\service;

use local_curriculum\model\area;
use local_curriculum\model\subject;
use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

abstract class AreaManager {
    public static function create_area(int $planid, string $name) : int {
        if(!PlanManager::is_active($planid)){
            throw new moodle_exception('planisnotactive','local_curriculum');
        }

        $name = trim($name);

        if ($name == ''){
            throw new moodle_exception('emptyname','local_curriculum');
        }

        $next = (self::get_last_order($planid) ?? 0) + 1;

        return area::create([
            'planid' => $planid,
            'areaname' => $name,
            'sortorder' => $next
        ]);
    }
    public static function delete_area(int $areaid): void{
        try {
            $area = area::get_by_id($areaid);
        } catch (\Throwable $th) {
            throw new moodle_exception('areanotfound',
                            'local_curriculum');
        }
        $planid = $area -> planid;
        if(!PlanManager::is_active($planid)){
            throw new moodle_exception('planisnotactive','local_curriculum');
        }
        area::transactional(function() use ($areaid){
            subject::set(
                'areaid',
                null,
                ['areaid'=>$areaid]
            );
            area::delete($areaid);
        });
    }

    public static function update_area(int $areaid, string $areaname, int $sortorder): void{
        area::update($areaid,[
            'areaname' => $areaname,
            'sortorder' => $sortorder
        ]);
    }

    public static function get_last_order(int $planid): ?int{
        return area::last_order($planid);
    }

    public static function get_by_id(int $areaid): ?object{
        return area::get_by_id($areaid);
    }
}