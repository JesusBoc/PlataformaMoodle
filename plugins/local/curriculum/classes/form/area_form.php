<?php

namespace local_curriculum\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class plan_form extends \moodleform {
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
        
    }
    public function validation($data, $files)
    {
        return parent::validation($data, $files);
    }
}