<?php

namespace local_academicload\form;

use core_course_category;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

class SelectLevelForm extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $categories = core_course_category::make_categories_list();
        $mform->addElement(
            'select',
            'categoryid',
            get_string(
                'getacademiclevel',
                'local_academicload'
            ),
            $categories
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->addRule('categoryid', null, 'required');

        $this->add_action_buttons(
            true,
            get_string(
                'assignteacher',
                'local_academicload'
            )
        );
    }
}