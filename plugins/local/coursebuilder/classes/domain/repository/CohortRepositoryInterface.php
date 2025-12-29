<?php
namespace local_coursebuilder\domain\repository;

defined('MOODLE_INTERNAL') || die();

use local_coursebuilder\domain\model\CohortDTO;

interface CohortRepositoryInterface {
    /**
     * @return CohortDTO[]
     */
    public function get_by_level(int $categoryid): array;
}