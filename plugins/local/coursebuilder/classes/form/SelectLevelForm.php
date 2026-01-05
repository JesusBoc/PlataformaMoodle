<?php

namespace local_coursebuilder\form;

use core_course_category;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class SelectLevelForm extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $categories = core_course_category::make_categories_list();
        $mform->addElement('select', 'categoryid', 'Nivel educativo', $categories);
        $mform->setType('categoryid', PARAM_INT);
        $mform->addRule('categoryid', null, 'required');

        $this->add_action_buttons(true, 'Generar vista previa');
    }
}
