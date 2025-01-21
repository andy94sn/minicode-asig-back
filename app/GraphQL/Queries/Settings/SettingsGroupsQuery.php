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

class SettingsGroupsQuery extends Query
{
    protected $attributes = [
        'name' => 'getSettingsGroups',
        'description' => 'Return Settings Groups',
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
                'description' => 'Group'
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
