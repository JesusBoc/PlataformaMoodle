<?php

namespace local_academicload\domain;

defined('MOODLE_INTERNAL') || die();

class TeachingAssignment {
    private int $id;
    private int $teacherid;
    private int $subjectid;
    private int $cohortid;
    private int $roleid;
    private string $status;

    /**
     * @param int $id
     * @param int $teacherid
     * @param int $subjectid
     * @param int $cohortid
     * @param int $roleid
     * @param string $status
     */
    public function __construct(
        int $id,
        int $teacherid,
        int $subjectid,
        int $cohortid,
        int $roleid,
        string $status
    )
    {
        if(
            $teacherid <= 0
            || $subjectid <= 0
            || $cohortid <= 0
            || $roleid <= 0
        ){
            throw new DomainException('invalididentifier');
        }

        if(!AssignmentStatus::is_valid($status)){
            throw new DomainException('invalidstatus');
        }

        $this->id = $id;
        $this->teacherid = $teacherid;
        $this->subjectid = $subjectid;
        $this->cohortid = $cohortid;
        $this->roleid = $roleid;
        $this->status = $status;
    }

    public function get_id(): int {
        return $this->id;
    }

    public function get_teacherid(): int {
        return $this->teacherid;
    }

    public function get_subjectid(): int {
        return $this->subjectid;
    }

    public function get_cohortid(): int {
        return $this->cohortid;
    }

    public function get_roleid(): int {
        return $this->roleid;
    }

    public function get_status(): string {
        return $this->status;
    }

    public function setID(int $newID){
        $this->id = $newID;
    }

    public function mark_pending(): void{
        $this->status = AssignmentStatus::PENDING;
    }

    public function mark_applied(): void{
        $this->status = AssignmentStatus::APPLIED;
    }

    public function mark_error(): void{
        $this->status = AssignmentStatus::ERROR;
    }
    /**
     * Static method for creating TeachingAssignments's
     * The default value for $roleid is 3 because of
     * that is the id for the editingteacher role
     */
    public static function create(
        int $teacherid,
        int $subjectid,
        int $cohortid,
        int $roleid = 3
    ): TeachingAssignment {
        return new TeachingAssignment(
            0,
            $teacherid,
            $subjectid,
            $cohortid,
            $roleid,
            AssignmentStatus::PENDING
        );
    }

}