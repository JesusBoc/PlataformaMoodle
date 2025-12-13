<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for the curriculum plugin.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_curriculum_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // ===== Upgrade to 2025121300 =====
    if ($oldversion < 2025121300) {

        // Add fields to existing plan table.
        $table = new xmldb_table('local_curriculum_plan');

        // version
        $field = new xmldb_field('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 1, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // active
        $field = new xmldb_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, 'version');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // timemodified
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025121300, 'local', 'curriculum');
    }

    return true;
}

