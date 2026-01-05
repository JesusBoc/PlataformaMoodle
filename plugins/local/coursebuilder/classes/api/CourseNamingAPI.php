<?php

namespace local_coursebuilder\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Public API for course naming conventions.
 */
final class CourseNamingApi {

    /**
     * Build the course shortname for a subject in a cohort.
     */
    public static function build_shortname(
        string $subjectname,
        string $cohortname,
        int $year
    ): string {
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
        string $subjectname,
        string $cohortname,
        int $year
    ): string {
        $cohortcode = static::normalize_cohort_name($cohortname);
        return sprintf('%s - %s - %d',
            $subjectname,
            $cohortcode,
            $year
        );
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
