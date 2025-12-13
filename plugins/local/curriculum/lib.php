<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Get active curriculum plan for a category (level).
 *
 * @param int $categoryid
 * @return stdClass|false
 */
function local_curriculum_get_active_plan_by_category(int $categoryid) {
    global $DB;

    return $DB->get_record(
        'local_curriculum_plan',
        [
            'categoryid' => $categoryid,
            'active' => 1
        ],
        '*',
        IGNORE_MULTIPLE
    );
}

/**
 * Get subjects for a curriculum plan.
 *
 * @param int $planid
 * @return array
 */
function local_curriculum_get_plan_subjects(int $planid) {
    global $DB;

    return $DB->get_records(
        'local_curriculum_plan_subjects',
        ['planid' => $planid],
        'sortorder ASC'
    );
}

/**
 * Deactivate all plans for a given category.
 *
 * @param int $categoryid
 * @return void
 */
function local_curriculum_deactivate_plans_by_category(int $categoryid) {
    global $DB;

    $DB->set_field(
        'local_curriculum_plan',
        'active',
        0,
        ['categoryid' => $categoryid]
    );
}

/**
 * Create a new curriculum plan version for a category.
 *
 * @param int $categoryid
 * @param string $name
 * @return int New plan ID
 */
function local_curriculum_create_plan(int $categoryid, string $name) {
    global $DB;

    // Get last version.
    $sql = "
        SELECT MAX(version)
          FROM {local_curriculum_plan}
         WHERE categoryid = :categoryid
    ";

    $lastversion = $DB->get_field_sql($sql, ['categoryid' => $categoryid]);
    $newversion = ($lastversion ?? 0) + 1;

    // Enforce single active plan.
    local_curriculum_deactivate_plans_by_category($categoryid);

    $record = new stdClass();
    $record->categoryid = $categoryid;
    $record->name = $name;
    $record->version = $newversion;
    $record->active = 1;
    $record->timecreated = time();
    $record->timemodified = time();

    return $DB->insert_record('local_curriculum_plan', $record);
}
