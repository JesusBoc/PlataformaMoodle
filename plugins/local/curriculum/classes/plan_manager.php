<?php
namespace local_curriculum;

defined('MOODLE_INTERNAL') || die();

class plan_manager {

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
