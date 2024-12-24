<?php


namespace App\GraphQL\Queries\Settings;

use App\Models\Admin;
use App\Models\Setting;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SettingsQuery extends Query
{
    protected $attributes = [
        'name' => 'getSettings',
        'description' => 'Return Settings',
        'model' => Setting::class
    ];

    public function type(): Type
    {
        return GraphQL::type('SettingPagination');
    }

    public function args(): array
    {
        return [
            'group' => [
                'name' => 'group',
                'type' => Type::string(),
                'description' => 'Search By Group'
            ],
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Pagination'
            ]
        ];
    }


    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);
            $group = HelperService::clean($args['group']);

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-settings')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Setting::query();
            $perPage = $args['perPage'] ?? 10;

            if ($group) {
                $query->where('group', $group);
            }

            $settings = $query->paginate($perPage);
            return [
                'data' => $settings->items(),
                'meta' => [
                    'total' => $settings->total(),
                    'current_page' => $settings->currentPage(),
                    'last_page' => $settings->lastPage(),
                    'per_page' => $settings->perPage(),
                    'from' => $settings->firstItem(),
                    'to' => $settings->lastItem()
                ]
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
