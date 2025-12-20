<?php

namespace local_curriculum\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class subject_form extends \moodleform {
    public function definition()
    {
        $mform = $this->_form;
        $subject = $this->_customdata['subject'] ?? null;
        $areas = $this->_customdata['areas'];
        $planid = $this->_customdata['planid'];

        $areaoptions = [0 => get_string('noarea', 'local_curriculum')] + $areas;

        if($subject){
            $mform->addElement('hidden', 'id', $subject->id);
            $mform->setType('id', PARAM_INT);
        }

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $mform->addElement(
                    'select',
                    'areaid',
                    get_string('area', 'local_curriculum'),
                    $areaoptions
                );

        $mform->addElement('text', 
                        'subjectname', 
                        get_string('subjectname',
                        'local_curriculum'));
                        
        $mform->setType('subjectname', PARAM_NOTAGS);
        $mform->addRule('subjectname', null, 'required', null, 'client');

        $mform->addElement('text', 'ihs', get_string('ihs', 'local_curriculum'));
        $mform->setType('ihs', PARAM_INT);

        if($subject){
            $mform->setDefault('subjectname',$subject->subjectname);
            $mform->setDefault('ihs', $subject->ihs);
            $mform->setDefault('areaid', $subject->areaid);
        }
        $this->add_action_buttons(true, get_string('save'));
    }
    public function validation($data, $files)
    {
        $errors = []; 

        if (trim($data['subjectname']) === '') {
            $errors['subjectname'] = get_string('emptyname', 'local_curriculum');
        }
        if ($data['ihs'] <=0 ) {
            $errors['ihs'] = get_string('invalidihs', 'local_curriculum');
        }

        return $errors;
    }
}