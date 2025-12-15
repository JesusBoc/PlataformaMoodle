<?php

namespace local_curriculum\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class plan_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        $plan = $this->_customdata['plan'] ?? null;
        $categoryid = $this->_customdata['categoryid'];

        if ($plan) {
            $mform->addElement('hidden', 'id', $plan->id);
            $mform->setType('id', PARAM_INT);
        }
        $mform->addElement('hidden', 'categoryid', $categoryid);
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', $categoryid);

        $mform->addElement('text', 'name', get_string('planname', 'local_curriculum'));
        $mform->setType('name', PARAM_TEXT); 
        $mform->addRule('name', null, 'required', null, 'client');

        if ($plan) {
            $mform->setDefault('name', $plan->name);
        }
        $this->add_action_buttons(true, get_string('save'));
    }

    public function validation($data, $files) {
        $errors = []; 

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('emptyname', 'local_curriculum');
        }
        if (empty($data['categoryid'])) {
            $errors['categoryid'] = get_string('invalidcategory', 'local_curriculum');
        }

        return $errors;
    }
}