<?php

use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Get active curriculum plan for a category (level).
 *
 * @param int $categoryid
 * @return stdClass|false
 */


/**
 * Get subjects for a curriculum plan.
 *
 * @param int $planid
 * @return array
 */


/**
 * Deactivate all plans for a given category.
 *
 * @param int $categoryid
 * @return void
 */


/**
 * Create a new curriculum plan version for a category.
 *
 * @param int $categoryid
 * @param string $name
 * @return int New plan ID
 */


/**
 * Check if a curriculum plan is editable.
 *
 * @param int $planid
 * @return bool
 */
function local_curriculum_plan_is_editable(int $planid): bool{
    global $DB;

    return $DB->record_exists(
        'local_curriculum_plan',
        [
            'id' => $planid,
            'active' => 1
        ]
    );
}

/**
 * Add a subject to a curriculum plan.
 *
 * @param int $planid
 * @param string $name
 * @param int $ihs
 * @return int New subject ID
 * @throws moodle_exception
 */
function local_curriculum_add_subject(int $planid, string $name, int $ihs): int{
    global $DB;

    if (!local_curriculum_plan_is_editable($planid)){
        throw new moodle_exception('planisnoteditable', 'local_curriculum');
    }

    $name = trim($name);

    if ($name == ''){
        throw new moodle_exception('emptysubjectname','local_curriculum');
    }

    if ($ihs <=0){
        throw new moodle_exception('invalidihs','local_curriculum');
    }


    $sql = "
        SELECT MAX(sortorder)
        FROM {local_curriculum_plan_subjects}
        WHERE planid = :planid
    ";

    $max = $DB -> get_field_sql($sql,
                            ['planid'=>$planid]
    );
    $nextsort = ($max ?? 0) + 1;
    $record = new stdClass();
    $record->planid = $planid;
    $record->subjectname = $name;
    $record->ihs = $ihs;
    $record->timecreated = time();
    $record->timemodified = time();
    $record->sortorder = $nextsort;

    return $DB->insert_record('local_curriculum_plan_subjects',$record);
}

/**
 * Update a subject in a curriculum plan.
 *
 * @param int $subjectid
 * @param string $name
 * @param int $ihs
 * @return void
 * @throws moodle_exception
 */
function local_curriculum_update_subject(int $subjectid, string $name, int $ihs): void{
    global $DB;

    $subject = $DB->get_record(
        'local_curriculum_plan_subjects',
        ['id' => $subjectid],
        '*',
        MUST_EXIST
    );

    if (!local_curriculum_plan_is_editable($subject->plaind)){
        throw new moodle_exception('planisnoteditable','local_curriculum');
    }

    $name = trim($name);

    if ($name == ''){
        throw new moodle_exception('emptysubjectname','local_curriculum');
    }

    if ($ihs <=0){
        throw new moodle_exception('invalidihs','local_curriculum');
    }

    $subject->subjectname = $name;
    $subject->ihs = $ihs;
    $subject->timemodified = time();

    $DB->uptade_record('local_curriculum_plan_subjects',$subject);
}

/**
 * Add an area to a curriculum plan.
 *
 * @param int $planid
 * @param string $name
 * @return int New area ID
 * @throws moodle_exception
 */
function local_curriculum_add_areas(int $planid, string $name): int{
    global $DB;

    if (!local_curriculum_plan_is_editable($planid)){
        throw new moodle_exception('planisnoteditable', 'local_curriculum');
    }

    $name = trim($name);

    if ($name == ''){
        throw new moodle_exception('emptysubjectname','local_curriculum');
    }

    $sql = "
        SELECT MAX(sortorder)
        FROM {local_curriculum_plan_areas}
        WHERE planid = :planid
    ";

    $max = $DB -> get_field_sql($sql,
                            ['planid'=>$planid]
    );
    $nextsort = ($max ?? 0) + 1;
    $record = new stdClass();
    $record->planid = $planid;
    $record->areaname = $name;
    $record->timecreated = time();
    $record->timemodified = time();
    $record->sortorder = $nextsort;

    return $DB->insert_record('local_curriculum_plan_areas',$record);
}

/**
 * Update an area in a curriculum plan.
 *
 * @param int $subjectid
 * @param string $name
 * @return void
 * @throws moodle_exception
 */
function local_curriculum_update_area(int $areaid, string $name): void{
    global $DB;

    $area = $DB->get_record(
        'local_curriculum_plan_areas',
        ['id' => $areaid],
        '*',
        MUST_EXIST
    );

    if (!local_curriculum_plan_is_editable($area->plaind)){
        throw new moodle_exception('planisnoteditable','local_curriculum');
    }

    $name = trim($name);

    if ($name == ''){
        throw new moodle_exception('emptysubjectname','local_curriculum');
    }

    $area->subjectname = $name;
    $area->timemodified = time();

    $DB->uptade_record('local_curriculum_plan_areas',$area);
}