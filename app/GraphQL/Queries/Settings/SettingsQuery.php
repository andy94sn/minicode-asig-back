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
        return Type::listOf(GraphQL::type('Setting'));
    }

    public function args(): array
    {
        return [
            'group' => [
                'name' => 'group',
                'type' => Type::string(),
                'description' => 'Search By Group'
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

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-settings')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Setting::query();

            if (isset($args['group'])) {
                $group = HelperService::clean($args['group']);
                $query->where('group', $group);
            }

            return $query->get();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
