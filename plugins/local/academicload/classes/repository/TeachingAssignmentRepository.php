<?php

namespace local_academicload\repository;

use local_academicload\domain\AssignmentStatus;
use local_academicload\domain\TeachingAssignment;

defined('MOODLE_INTERNAL') || die();

class TeachingAssignmentRepository {
    private $table = 'local_academicload_assign';

    
    /**
     * Insert a new teaching assignment
     * @param TeachingAssignment $assignment
     * The assignment to insert in the DB
     * 
     * @return int New assignment ID 
     */
    public function insert(TeachingAssignment $assignment): int{
        global $DB;

        $record['teacherid'] = $assignment->get_teacherid();
        $record['subjectid'] = $assignment->get_subjectid();
        $record['cohortid'] = $assignment->get_cohortid();
        $record['roleid'] = $assignment->get_roleid();
        $record['status'] = $assignment->get_status();
        $record['timecreated'] = time();
        $record['timemodified'] = time();

        return $DB->insert_record($this->table, (object)$record);;
    }

    public function get(int $id): ?TeachingAssignment {
        global $DB;

        $record = $DB->get_record($this->table,
            ['id' => $id]
        );

        if(!$record){
            return null;
        }

        return $this->map_to_entity($record);
    }

    public function get_unique(
        int $teacherid,
        int $subjectid,
        int $cohortid
    ){
        global $DB;
    
        return $DB->get_record(
            $this->table,
            [
                'teacherid' => $teacherid,
                'subjectid' => $subjectid,
                'cohortid' => $cohortid
            ]
        ) ?: null;
    }

    /**
     * Find pending assignments for a given subject
     * 
     * @return TeachingAssignment[]
     */
    public function get_pending_by_subject(int $subjectid): array{
        global $DB;

        $records = $DB->get_records($this->table, [
            'subjectid' => $subjectid,
            'status' => AssignmentStatus::PENDING
        ]);

        return $records ? $this->map_records($records) : [];
    }

    /**
     * Find pending assignments for a given cohort
     * 
     * @return TeachingAssignment[]
     */
    public function get_pending_by_cohort(int $cohortid): array{
        global $DB;

        $records = $DB->get_records($this->table, [
            'cohortid' => $cohortid,
            'status' => AssignmentStatus::PENDING
        ]);

        return $records ? $this->map_records($records) : [];
    }

    /**
     * Find pending assignments for a given cohort
     * 
     * @return ?TeachingAssignment
     */
    public function get_by_cohortid_subjectid(int $cohortid, int $subjectid): ?TeachingAssignment{
        global $DB;

        $record = $DB->get_record($this->table, [
            'cohortid' => $cohortid,
            'subjectid' => $subjectid,
        ]);

        return $record ? $this->map_to_entity($record) : null;
    }

    /**
     * Find retryable assignments
     * 
     * @return TeachingAssignment[]|null
     */
    public function get_retryable(): array {
        global $DB;

        $sql = "status = :pending OR status = :error";

        $params = [
            'pending' => AssignmentStatus::PENDING,
            'error' => AssignmentStatus::ERROR,
        ];

        $records = $DB->get_records_select(
            $this->table,
            $sql,
            $params
        );

        return $records ? $this->map_records($records) : [];
    }

    public function mark_pending(int $id): void {
        $this->update_record($id, [], AssignmentStatus::PENDING);
    }

    public function mark_applied(int $id, int $courseid): void {
        $data['courseid'] = $courseid;
        $data['errormessage'] = null;
        $this->update_record($id, $data, AssignmentStatus::APPLIED);
    }

    public function mark_error(int $id, string $error): void {
        $data['errormessage'] = $error;
        $this->update_record($id, $data, AssignmentStatus::ERROR);
    }

    public function delete(int $id): bool {
        global $DB;

        if (!$DB->record_exists($this->table, ['id' => $id])) {
            return false;
        }

        return $DB->delete_records($this->table, ['id' => $id]);
    }

    public function unassign(int $cohortid, int $subjectid){
        $assignment = $this->get_by_cohortid_subjectid($cohortid, $subjectid);
        if(!$assignment){
            return;
        }
        $this->delete($assignment->get_id());
    }

    public function update_teacher(int $id, int $newTeacherID){
        $this->update_record(
            $id,
            ['teacherid' => $newTeacherID]
        );
    }

    private function map_to_entity(object $record): TeachingAssignment{
        return new TeachingAssignment(
            (int)$record->id,
            (int)$record->teacherid,
            (int)$record->subjectid,
            (int)$record->cohortid,
            (int)$record->roleid,
            (string)$record->status
        );
    }

    /**
     * Maps an array of record for TeachingAsignments to an array of TeachingAssignments
     * 
     * @return TeachingAssignment[]
     */
    private function map_records(array $records): array{
        $result = [];
        foreach($records as $record){
            $result[] = $this->map_to_entity($record);
        }
        return $result;
    }

    private function update_record(int $id, array $data, string $status = null): void{
        global $DB;

        $data['id'] = $id;
        $data['timemodified'] = time();
        if($status){
            $data['status'] = $status;
        }

        $DB->update_record($this->table, (object)$data);
    }


}