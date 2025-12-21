<?php
namespace local_curriculum\form;

use core_form\dynamic_form;
use context_system;
use local_curriculum\service\AreaManager;

defined('MOODLE_INTERNAL') || die();

class area_modal extends dynamic_form {

    public function set_data_for_dynamic_submission(): void {
        $areaid = $this->optional_param('areaid', 0, PARAM_INT);

        if ($areaid) {
            $area = AreaManager::get_by_id($areaid);

            if ($area) {
                $this->set_data([
                    'areaid' => $areaid,
                    'areaname' => $area->areaname,
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

        $planid = $this->optional_param('planid', 0, PARAM_INT);
        $areaid = $this->optional_param('areaid', 0, PARAM_INT);

        $mform->addElement('hidden', 'areaid', $areaid);
        $mform->setType('areaid', PARAM_INT);

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $mform->addElement('text', 'areaname',
            get_string('areaname', 'local_curriculum'));
        $mform->setType('areaname', PARAM_NOTAGS);
        $mform->addRule('areaname', null, 'required', null, 'client');
    }

    public function process_dynamic_submission() {
        $data = $this->get_data();
        if (!empty($data->areaid)) {
            AreaManager::update_area(
                $data->areaid,
                $data->areaname
            );
        } else {
            AreaManager::create_area(
                $data->planid,
                $data->areaname
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
