<?php
namespace local_curriculum\service;

use block_mockblock\search\area;
use local_curriculum\model\subject;
use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

abstract class SubjectManager
{
    public static function get_by_id(int $subjectid): ?object{
        return subject::get_by_id($subjectid);
    }
    public static function get_by_area(int $areaid): array{
        return subject::get_many_by([
            'areaid' => $areaid
        ]);
    }
    public static function get_outside_area(int $planid): array{
        return subject::get_many_by([
            'areaid' => null,
            'planid' => $planid
        ]);
    }
    public static function update_order(int $subjectid, int $sortorder): bool{
        return subject::update($subjectid, [
            'sortorder' => $sortorder
        ]);
    }
    public static function create_subject(int $planid, 
                    string $subjectname, 
                    ?int $areaid, 
                    int $ihs): int
    {
        $plan = self::validate_plan($planid);
        $subjectname = trim($subjectname);

        if ($subjectname === '') {
            throw new moodle_exception('emptyname', 'local_curriculum');
        }

        if ($areaid !== null) {
            self::validate_area($areaid, $plan->id);
        }

        if ($ihs <= 0) {
            throw new moodle_exception('invalidihs', 'local_curriculum');
        }

        $next = (subject::last_order($planid) ?? 0) + 1;

        return subject::create([
            'planid' => $planid,
            'subjectname' => $subjectname,
            'areaid' => $areaid,
            'ihs' => $ihs,
            'sortorder' => $next
        ]);
    }

    public static function get_by_plan(int $planid): ?array{
        self::validate_plan($planid);
        return subject::get_by_plan($planid);
    }

    public static function update_all(int $subjectid, string $subjectname, int $ihs, int $areaid = null){
        subject::transactional(function() use ($subjectid, $subjectname, $areaid, $ihs){
            self::update_area($subjectid, $areaid);
            self::update_ihs($subjectid, $ihs);
            self::update_name($subjectid, $subjectname);
        });
    }

    public static function update_name(string $subjectid, string $subjectname){
        self::validate_subject($subjectid);
        $subjectname = trim($subjectname);
        if($subjectname == ''){
            throw new moodle_exception(
                                'emptyname',
                                'local_curriculum'
            );
        }
        subject::update($subjectid,
                        ['subjectname', $subjectname]
        );
    }

    public static function update_ihs(int $subjectid, int $ihs){
        self::validate_subject($subjectid);
        if($ihs <= 0){
            throw new moodle_exception(
                                'invalidihs',
                                'local_curriculum'
            );
        }
        subject::update($subjectid,
                        ['ihs', $ihs]
        );
    }

    public static function update_area(int $subjectid, int $newareaid = null): bool{
        try {
            $subject = self::get_by_id($subjectid);
        } catch (\Throwable $th) {
            throw new moodle_exception('areanotfound',
                        'local_curriculum');
        }
        if($newareaid !== null){
            self::validate_area($newareaid, $subject->planid);
        }
        return subject::update($subjectid, [
            'areaid' => $newareaid
        ]);
    }

    public static function delete_by_id(int $subjectid): bool {
        return subject::delete($subjectid);
    }

    private static function validate_subject(int $subjectid){
        try {
            $subject = self::get_by_id($subjectid);
        } catch (\Throwable $th) {
            throw new moodle_exception('subjectnotfound',
                        'local_curriculum');
        }
    }

    private static function validate_plan(int $planid): ?object
    {
        try {
            $plan = PlanManager::get_by_id($planid);
            if ($plan->active == 0) {
                throw new moodle_exception('planisnotactive', 'local_curriculum');
            }
            return $plan;
        } catch (\Throwable $th) {
            debugging($th->getMessage(), DEBUG_DEVELOPER);
            throw new moodle_exception('plannotfound', 'local_curriculum');
        }
    }

    private static function validate_area(int $areaid, int $planid)
    {
        try {
            $area = AreaManager::get_by_id($areaid);
            if ($area->planid != $planid) {
                throw new moodle_exception('areanotinplan', 'local_curriculum');
            }
        } catch (\Throwable $th) {
            debugging($th->getMessage(), DEBUG_DEVELOPER);
            throw new moodle_exception('areanotfound', 'local_curriculum');
        }
    }
}

