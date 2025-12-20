<?php
namespace local_curriculum\form;

use core_form\dynamic_form;
use context_system;
use local_curriculum\service\SubjectManager;
use local_curriculum\service\AreaManager;

defined('MOODLE_INTERNAL') || die();

class subject_modal extends dynamic_form {

    public function set_data_for_dynamic_submission(): void {
        $subjectid = $this->optional_param('subjectid', 0, PARAM_INT);

        if ($subjectid) {
            // Recupera la asignatura desde tu servicio.
            $subject = SubjectManager::get_by_id($subjectid);

            if ($subject) {
                $this->set_data([
                    'subjectid' => $subjectid,
                    'subjectname' => $subject->subjectname,
                    'ihs' => $subject->ihs,
                    'areaid' => $subject->areaid,
                ]);
            }
        }
    }

    protected function get_context_for_dynamic_submission(): \context {
        return context_system::instance();
    }

    protected function check_access_for_dynamic_submission(): void {
        $planid = $this->optional_param('planid', 0, PARAM_INT);

        if (!$planid) {
            throw new \moodle_exception('invalidplan', 'local_curriculum');
        }

        $context = \context_system::instance();

        if (!has_capability('local/curriculum:manageplans', $context)) {
            throw new \required_capability_exception(
                $context,
                'local/curriculum:manageplans',
                'nopermissions',
                ''
            );
        }
    }

    public function definition() {
        $mform = $this->_form;

        $subjectid = $this->optional_param('subjectid', 0, PARAM_INT);
        $planid = $this->optional_param('planid', 0, PARAM_INT);
        $areaid = $this->optional_param('areaid', 0, PARAM_INT);

        $areasraw = AreaManager::get_by_plan($planid);
        $areas = [];

        foreach ($areasraw as $area) {
            $areas[$area->id] = $area->name;
        }

        $areaoptions = [0 => get_string('noarea', 'local_curriculum')] + $areas;

        $mform->addElement('hidden', 'subjectid', $subjectid);
        $mform->setType('subjectid', PARAM_INT);

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $mform->addElement('text', 'subjectname',
            get_string('subjectname', 'local_curriculum'));
        $mform->setType('subjectname', PARAM_NOTAGS);
        $mform->addRule('subjectname', null, 'required', null, 'client');

        $mform->addElement(
                    'select',
                    'areaid',
                    get_string('area', 'local_curriculum'),
                    $areaoptions
                );

        $mform->addElement('text', 'ihs', get_string('ihs', 'local_curriculum'));
        $mform->setType('ihs', PARAM_INT);
    }

    public function process_dynamic_submission() {
        $data = $this->get_data();
        $areaid = $data->areaid == 0 ? null : $data->areaid;
        if (!empty($data->subjectid)) {
            SubjectManager::update_all($data->subjectid,
                        $data->subjectname,
                        $data->ihs
                );
        } else {
            SubjectManager::create_subject($data->planid, 
                            $data->subjectname,
                            $areaid,
                            $data->ihs
                        );
        }
        return [
            'id' => $data->id ?? null
        ];
    }

    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        return new \moodle_url('/local/curriculum/editor.php');
    }
}
