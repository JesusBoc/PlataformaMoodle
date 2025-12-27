<?php
namespace local_curriculum\service;

defined('MOODLE_INTERNAL') || die();

class IndexService {

    public static function get_structure(): array {
        $categories = PlanManager::get_categories();
        $structure = [];

        foreach ($categories as $category) {
            $structure[] = [
                'id' => $category->id,
                'name' => $category->name,
                'plans' => self::get_plans_structure($category->id),
                'addurl' => (new \moodle_url(
                    '/local/curriculum/plan.php',
                    ['categoryid' => $category->id]
                ))->out(false),
                'cloneurl' => (new \moodle_url(
                    '/local/curriculum/clone.php',
                    ['categoryid' => $category->id]
                ))->out(false),
            ];
        }

        return $structure;
    }

    private static function get_plans_structure(int $categoryid): array {
        $result = [];
        $plans = PlanManager::get_by_category($categoryid);

        foreach ($plans as $plan) {
            $result[] = [
                'id' => $plan->id,
                'name' => $plan->name,
                'version' => $plan->version,
                'active' => $plan->active,
                'editurl' => (new \moodle_url(
                    '/local/curriculum/editor.php',
                    ['planid' => $plan->id]
                ))->out(false),
            ];
        }

        return $result;
    }
}