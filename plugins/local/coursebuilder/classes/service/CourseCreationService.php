<?php
namespace local_coursebuilder\service;

require_once($CFG->dirroot . '/course/lib.php');

use core\router\schema\objects\array_of_things;
use local_coursebuilder\domain\model\CourseAction;
use local_coursebuilder\domain\model\SubjectDTO;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class CourseCreationService{

    /**
     * @param array<int, CourseAction[]> $actionmap
     * @return array<int, array{action: string, subject: string, cohort: string, year: int, shortname: string, fullname: string, exists: bool}[]> $preview
     */
    public function build_preview(array $actionmap): array{
        $preview = [];

        foreach ($actionmap as $cohortid => $actions) {
            foreach ($actions as $action) {

                $preview[$cohortid][] = [
                    'action'     => $action->action,
                    'subject'    => $action->subject->name,
                    'cohort'     => $action->cohortname,
                    'year'       => $action->year,
                    'shortname'  => $this->generate_shortname(
                        $action->subject,
                        $action->cohortname,
                        $action->year
                    ),
                    'fullname'   => $this->generate_fullname(
                        $action->subject,
                        $action->cohortname,
                        $action->year
                    ),
                    'exists'     => $action->existing !== null,
                ];
            }
        }

        return $preview;
    }
    /**
    * Ejecuta la creación de cursos a partir de las acciones calculadas.
    *
    * @param array<int, CourseAction[]> $actionmap
    *  Mapa donde:
    *   - la clave es el ID de la cohorte
    *   - el valor es un array de ActionMaps asociadas a esa cohorte
    *
    * @return void
    */
    public function execute(array $actionmap, int $categoryid){
        foreach($actionmap as $cohortid => $actions){
            foreach($actions as $action){
                if($action->action !== CourseAction::CREATE){
                    continue;
                }
                $fullname = $this->generate_fullname(
                    $action->subject,
                    $action->cohortname,
                    $action->year
                );
                $shortname = $this->generate_shortname(
                    $action->subject,
                    $action->cohortname,
                    $action->year
                );

                $course = new stdClass();
                $course->fullname = $fullname;
                $course->shortname = $shortname;
                $course->fullname = $fullname;
                $course->category = $categoryid;
                $course->visible = 1;

                $created = create_course($course);

                $this->enrol_cohort(
                    $created->id,
                    $cohortid
                );
            }
        }
    }
    private function generate_shortname(
        SubjectDTO $subject,
        string $cohortname,
        int $year
    ): string{

        $subjeccode = $this->generate_subject_code($subject->name);
        $cohortcode = $this->normalize_cohort_name($cohortname);

        return sprintf('%s-%s-%d',
            $subjeccode,
            $cohortcode,
            $year
        );
    }

    private function generate_subject_code(
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

    private function normalize_cohort_name(
        string $cohortname
    ): string{
        // Elimina espacios
        $cohort = strtoupper(trim($cohortname));

        // Quita cualquier cosa que no sea alfanumérica
        return preg_replace('/[^A-Z0-9]/', '', $cohort);
    }

    private function generate_fullname(
        SubjectDTO $subject,
        string $cohortname,
        int $year
    ): string{
        $cohortcode = $this->normalize_cohort_name($cohortname);
        return sprintf('%s - %s - %d',
            $subject->name,
            $cohortcode,
            $year
        );
    }
    private function enrol_cohort(int $courseid, int $cohortid): void
    {
        global $DB;
        $plugin = enrol_get_plugin('cohort');
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);

        $instances = enrol_get_instances($courseid, true);

        foreach ($instances as $i) {
            if ($i->enrol === 'cohort'
            && (int)$i->customint1 === $cohortid
            ) {
                if ((int)$i->roleid === $studentroleid) {
                    return;
                }
                $plugin->delete_instance($i);
            }
        }
    
        $plugin->add_instance(
            (object)['id' => $courseid],
            [
                'name'       => 'Cohorte',
                'status'     => ENROL_INSTANCE_ENABLED,
                'customint1' => $cohortid,
                'roleid'     => $studentroleid,
            ]
        );
    }
}