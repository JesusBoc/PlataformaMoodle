<?php

namespace local_coursebuilder\api;

use core\exception\moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Public API for course naming conventions.
 */
final class CourseNamingApi {

    /**
     * Build the course shortname for a subject in a cohort.
     */
    public static function build_shortname(
        int $subjectid,
        int $cohortid,
    ): string {

        $subjectname = static::get_subject_name($subjectid);
        [$cohortname, $year] = static::get_cohort_name($cohortid);
        
        $subjectcode = static::generate_subject_code($subjectname);
        $cohortcode = static::normalize_cohort_name($cohortname);

        return sprintf('%s-%s-%d',
            $subjectcode,
            $cohortcode,
            $year
        );
    }

    /**
     * Build the full course name.
     */
    public static function build_fullname(
        int $subjectid,
        int $cohortid,
    ): string {
        $subjectname = static::get_subject_name($subjectid);
        [$cohortname, $year] = static::get_cohort_name($cohortid);
        $cohortcode = static::normalize_cohort_name($cohortname);
        return sprintf('%s - %s - %d',
            $subjectname,
            $cohortcode,
            $year
        );
    }

    private static function get_subject_name(int $subjectid): string{
        global $DB;

        $subject = $DB->get_record(
            'local_curriculum_plan_subjects',
            ['id' => $subjectid]
        );

        return $subject->subjectname;
    }

    private static function get_cohort_name(int $cohortid): array{
        global $DB;

        $cohort = $DB->get_record(
            'cohort',
            ['id' => $cohortid]
        );

        $parts = explode(' - ', $cohort->name);
        if(count($parts) !== 2){
            throw new moodle_exception(
                'invalid_cohort_name',
                'local_coursebuilder'
            );
        }

        [$name, $year] = $parts;

        return [(string)$name, (int)$year];
    }

    private static function generate_subject_code(
        string $subjectname
    ): string{
        // Elimina tildes y caracteres especiales
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $subjectname);

        // Solo letras y espacios
        $normalized = preg_replace('/[^A-Za-z ]/', '', $normalized);

        // Divide en palabras
        $words = explode(' ', strtoupper(trim($normalized)));

        // Inicializa el valor de retorno como un string vacío
        $base = '';

        foreach($words as $word){
            // Si la palabra tiene 2 o menos letras se ignora
            if(strlen($word) <= 2){
                continue;
            }
            // Añade la primera letra de cada palabra
            $base .= $word[0];
        }
        // Por convención del dominio educativo, la última palabra
        // siempre será una palabra significativa (>2 letras)
        // Añade dos letras más de la última palabra
        $last = $words[count($words) - 1];
        $base .= substr($last, 1, 2);

        return $base;
    }

    private static function normalize_cohort_name(
        string $cohortname
    ): string{
        // Elimina espacios
        $cohort = strtoupper(trim($cohortname));

        // Quita cualquier cosa que no sea alfanumérica
        return preg_replace('/[^A-Z0-9]/', '', $cohort);
    }
}
