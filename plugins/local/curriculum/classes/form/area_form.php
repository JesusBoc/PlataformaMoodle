<?php

namespace local_curriculum\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class area_form extends \moodleform {
    public function definition()
    {
        $mform = $this->_form;
        $area = $this->_customdata['area'] ?? null;
        $planid = $this->_customdata['planid'];

        if($area){
            $mform->addElement('hidden', 'id', $area->id);
            $mform->setType('id', PARAM_INT);
        }

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);
        $mform->setDefault('planid',$planid);

        $mform->addElement('text', 
                        'areaname', 
                        get_string('areaname',
                        'local_curriculum'));
        $mform->setType('areaname', PARAM_NOTAGS);
        $mform->addRule('areaname', null, 'required', null, 'client');

        if($area){
            $mform->setDefault('areaname',$area->areaname);
        }
        $this->add_action_buttons(true, get_string('save'));
    }
    public function validation($data, $files)
    {
        $errors = []; 

        if (trim($data['areaname']) === '') {
            $errors['areaname'] = get_string('emptyname', 'local_curriculum');
        }
        if (empty($data['planid'])) {
            $errors['planid'] = get_string('invalidplan', 'local_curriculum');
        }

        return $errors;
    }
}