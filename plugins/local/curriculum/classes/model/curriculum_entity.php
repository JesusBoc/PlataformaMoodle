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
    public static function get_one_by(
        array $conditions,
        string $fields = '*'
    ): ?object {

        global $DB;
    
        return $DB->get_record(
            static::$table,
            $conditions,
            $fields,
            IGNORE_MISSING
        ) ?: null;
    }
    public static function get_many_by(
    array $conditions,
    string $sort = '',
    string $fields = '*'
    ): array {
        global $DB;

        return $DB->get_records(
            static::$table,
            $conditions,
            $sort,
            $fields
        );
    }

    /**
    * @param string $field Field to edit
    * @param mixed $value Scalar value to set (int|string|float|bool|null)
    */
    public static function set(string $field, mixed $value, array $where) {
        global $DB;

        $DB->set_field(
            static::$table,
            $field,
            $value,
            $where
        );
    }

    protected static function now(): int {
        return time();
    }

    public static function transactional(callable $callback): void {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        
        try {
            $callback();
            $transaction->allow_commit();
        } catch (\Throwable $e) {
            $transaction->rollback($e);
        }
    }

}