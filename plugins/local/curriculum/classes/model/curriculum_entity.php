<?php

namespace local_curriculum\model;

defined('MOODLE_INTERNAL') || die();

abstract class curriculum_entity {
    protected static string $table;
    public static function get_by_id(int $id) {
        global $DB;
        return $DB->get_record(static::$table, ['id' => $id], MUST_EXIST);
    }

    public static function create(array $data): int {
        global $DB;

        $data['timecreated'] = static::now();
        $data['timemodified'] = static::now();

        return $DB->insert_record(static::$table, (object)$data);
    }

    public static function update(int $id, array $data): void{
        global $DB;

        $data['id'] = $id;
        $data['timemodified'] = static::now();

        $DB->update_record(static::$table,(object)$data);
    }

    public static function delete(int $id): bool {
        global $DB;

        if (!$DB->record_exists(static::$table, ['id' => $id])) {
            return false;
        }

        return $DB->delete_records(static::$table, ['id' => $id]);
    }

    public static function delete_by(string $param, int $value): bool{
        global $DB;

        if (!$DB->record_exists(static::$table, [$param => $value])) {
            return false;
        }

        return $DB->delete_records(static::$table, [$param => $value]);
    }

    protected static function now(): int {
        return time();
    }

}