<?php
namespace local_coursebuilder\service;

require_once($CFG->dirroot . '/course/lib.php');

use core\router\schema\objects\array_of_things;
use local_coursebuilder\api\CourseNamingApi;
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
                    'shortname'  => CourseNamingApi::build_shortname(
                        $action->subject->name,
                        $action->cohortname,
                        $action->year
                    ),
                    'fullname'   => CourseNamingApi::build_fullname(
                        $action->subject->name,
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
                $fullname = CourseNamingApi::build_fullname(
                    $action->subject->id,
                    $cohortid
                );
                $shortname = CourseNamingApi::build_shortname(
                    $action->subject->id,
                    $cohortid
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