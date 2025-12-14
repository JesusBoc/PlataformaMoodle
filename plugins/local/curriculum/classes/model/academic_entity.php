<?php

namespace local_curriculum\model;

defined('MOODLE_INTERNAL') || die();

abstract class academic_entity extends curriculum_entity{

    public static function get_by_plan(int $planid): array {
        global $DB;
        return $DB->get_records(static::$table, ['planid' => $planid], 'sortorder ASC');
    }
    
    public static function delete_by_plan(int $planid): bool{
        return static::delete_by('planid',$planid);
    }

    public static function last_order(int $planid): int{
        global $DB;

        $table = static::$table;
        $sql = "
        SELECT MAX(sortorder)
        FROM {{$table}}
        WHERE planid = :planid
        ";

        $max = $DB -> get_field_sql($sql,
                    ['planid'=>$planid]
        );
        return $max;
    }
}