<?php

namespace local_curriculum\service;

use local_curriculum\model\area;
use local_curriculum\model\subject;
use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

abstract class area_manager {
    public static function create_area(int $planid, string $name) : int {
        if(!plan_manager::is_active($planid)){
            throw new moodle_exception('planisnotactive','local_curriculum');
        }

        $name = trim($name);

        if ($name == ''){
            throw new moodle_exception('emptysubjectname','local_curriculum');
        }

        $next = (self::get_last_order($planid) ?? 0) + 1;

        return area::create([
            'planid' => $planid,
            'areaname' => $name,
            'sortorder' => $next
        ]);
    }
    public static function delete_area(int $areaid): void{
        $area = area::get_by_id($areaid);
        if (!$area) {
            throw new moodle_exception('areanotfound','local_curriculum');
        }
        $planid = $area -> planid;
        if(!plan_manager::is_active($planid)){
            throw new moodle_exception('planisnotactive','local_curriculum');
        }
        subject::set(
            'areaid',
            null,
            ['areaid'=>$areaid]
        );
        area::delete($areaid);
    }
    public static function get_last_order(int $planid): ?int{
        return area::last_order($planid);
    }
}