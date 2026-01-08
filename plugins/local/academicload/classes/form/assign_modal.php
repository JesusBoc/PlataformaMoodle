<?php
namespace local_academicload\form;

use context;
use core_form\dynamic_form;
use context_system;
use core\exception\required_capability_exception;
use local_academicload\domain\TeachingAssignment;
use local_academicload\repository\TeachingAssignmentRepository;
use local_academicload\repository\UserRepository;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class assign_modal extends dynamic_form {
    private TeachingAssignmentRepository $assignmentRepo;
    private UserRepository $userRepo;

    public function set_data_for_dynamic_submission(): void
    {
        $this->init_repositories();
        $subjectid = $this->optional_param('subjectid', 0, PARAM_INT);
        $cohortid = $this->optional_param('cohortid', 0, PARAM_INT);

        if($cohortid && $subjectid){
            $assignment = $this->assignmentRepo->get_by_cohortid_subjectid(
                $cohortid, 
                $subjectid
            );
            $this->set_data([
                    'subjectid' => $subjectid,
                    'cohortid' => $cohortid,
                    'teacherid' => $assignment?->get_teacherid() ?? 0
            ]);
        }
    }
    public function get_context_for_dynamic_submission(): context
    {
        return context_system::instance();
    }
    public function check_access_for_dynamic_submission(): void
    {
        $context = context_system::instance();
        if(!has_capability(
            'local/academicload:manageload',
            $context)){
            throw new required_capability_exception(
                $context,
                'local/academicload:manageload',
                'nopermissions',
                ''
            );
        }
    }
    public function definition()
    {
        $this->init_repositories();
        $mform = $this->_form;
        
        $subjectid = $this->optional_param('subjectid', 0, PARAM_INT);
        $cohortid  = $this->optional_param('cohortid', 0, PARAM_INT);
        $teacherid = $this->optional_param('teacherid', 0, PARAM_INT);

        $teachersraw = $this->userRepo->search_teachers();
        $teachers = [];
        foreach($teachersraw as $teacher){
            $teachers[$teacher->id] = $teacher->firstname . ' ' . $teacher->lastname;
        }

        $teacheroptions = [0 => get_string('unassigned', 'local_academicload')] + $teachers;

        $mform->addElement('hidden', 'subjectid', $subjectid);
        $mform->setType('subjectid', PARAM_INT);

        $mform->addElement('hidden', 'cohortid', $cohortid);
        $mform->setType('cohortid', PARAM_INT);

        $mform->addElement(
            'select',
            'teacherid',
            get_string('teacher', 'local_academicload'),
            $teacheroptions
        );
        $mform->setDefault('teacherid', $teacherid);
    }
    public function process_dynamic_submission()
    {
        $data = $this->get_data();
        if (empty($data->subjectid) || empty($data->cohortid)) {
            throw new \moodle_exception('invaliddata', 'local_academicload');
        }
        $teacherid = $data->teacherid == 0 ? null : $data->teacherid;
        if($teacherid){
            $assignment = $this->assignmentRepo->get_by_cohortid_subjectid(
               $data->cohortid,
               $data->subjectid
            );
            if(!$assignment){
                $assignment = TeachingAssignment::create(
                    $teacherid,
                    $data->subjectid,
                    $data->cohortid
                );
                $this->assignmentRepo->insert($assignment);
            } else {
                $this->assignmentRepo->update_teacher(
                    $assignment->get_id(),
                    $teacherid
                );
            }
        } else {
            $this->assignmentRepo->unassign(
                $data->cohortid,
                $data->subjectid
            );
        }
        return [
            'status' => 'ok'
        ];
    }
    protected function init_repositories(): void {
        if (!isset($this->assignmentRepo)) {
            $this->assignmentRepo = new TeachingAssignmentRepository();
            $this->userRepo = new UserRepository();
        }
    }
    protected function get_page_url_for_dynamic_submission(): moodle_url
    {
        global $PAGE;
        return $PAGE->url;
    }
}
