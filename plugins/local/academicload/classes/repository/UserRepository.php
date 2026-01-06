<?php

namespace local_academicload\repository;

use context_system;
use local_academicload\domain\AsTeacher;

defined('MOODLE_INTERNAL') || die();

class UserRepository{
public function get(int $userid): ?\stdClass {
        global $DB;

        return $DB->get_record('user', [
            'id' => $userid,
            'deleted' => 0,
            'suspended' => 0
        ]) ?: null;
    }

    public function get_fullname(int $userid): ?string {
        $user = $this->get($userid);
        return $user ? fullname($user) : null;
    }

    public function get_many(array $userids): array {
        global $DB;

        if (empty($userids)) {
            return [];
        }

        list($sql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        return $DB->get_records_select(
            'user',
            "id $sql AND deleted = 0",
            $params
        );
    }

    /**
     * Búsqueda de docentes por rol global
     * Uso exclusivo UI
     */
    public function search_teachers(string $query = ''): array {
        global $DB;

        $params = [
            'rolename' => 'docente_global'
        ];

        $sql = "
            SELECT u.id, u.firstname, u.lastname
              FROM {user} u
              JOIN {role_assignments} ra ON ra.userid = u.id
              JOIN {role} r ON r.id = ra.roleid
             WHERE r.shortname = :rolename
               AND u.deleted = 0
        ";

        if ($query !== '') {
            $sql .= " AND (
                u.firstname LIKE :q OR
                u.lastname LIKE :q OR
                u.email LIKE :q
            )";
            $params['q'] = "%$query%";
        }

        return $DB->get_records_sql($sql, $params);
    }
}