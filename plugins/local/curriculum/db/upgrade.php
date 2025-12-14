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

    // ===== Upgrade to 2025121301 =====
    if ($oldversion < 2025121301) {

        // Create new table 'local_curriculum_plan_areas'
        $table = new xmldb_table('local_curriculum_plan_areas');

        if (!$dbman->table_exists($table)) {
        
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('areaname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('plan_fk', XMLDB_KEY_FOREIGN, ['planid'], 'local_curriculum_plan', ['id']);
        
            $dbman->create_table($table);
        }

        // Add fields to existing subjects table.
        $table = new xmldb_table('local_curriculum_plan_subjects');

        // areaid
        $field = new xmldb_field('areaid', XMLDB_TYPE_INTEGER, '10',null,null,null,null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // timecreated
        $field = new xmldb_field('timecreated',
                                XMLDB_TYPE_INTEGER,
                                '10',
                                null,
                                XMLDB_NOTNULL,
                                null,
                                time());

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // timemodified
        $field = new xmldb_field('timemodified',
                                XMLDB_TYPE_INTEGER,
                                '10',
                                null,
                                XMLDB_NOTNULL,
                                null,
                                time(),
                                'timecreated');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Inicializar timemodified para registros antiguos
        $DB->execute(
            "UPDATE {local_curriculum_plan_subjects}
             SET timemodified = timecreated
             WHERE timemodified = 0"
        );

        $key = new xmldb_key('area_fk',
                            XMLDB_KEY_FOREIGN,
                            ['areaid'],
                            'local_curriculum_plan_areas',
                            ['id']
                        );
        $dbman->add_key($table, $key);

        $index = new xmldb_index('idx_plan_area',
                                XMLDB_INDEX_NOTUNIQUE,
                                ['planid','areaid']
                            );

        $dbman->add_index($table, $index);

        upgrade_plugin_savepoint(true, 2025121301, 'local', 'curriculum');
    }

    return true;
}

