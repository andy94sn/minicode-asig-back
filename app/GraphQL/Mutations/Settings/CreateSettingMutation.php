<?php

namespace App\GraphQL\Mutations\Settings;

use App\Enums\GroupType;
use App\Models\Admin;
use App\Models\Setting;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateSettingMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createSetting',
        'description' => 'Create Setting'
    ];

    public function type(): Type
    {
        return GraphQL::type('Setting');
    }

    public function args(): array
    {
        return [
            'group' => [
                'name' => 'group',
                'type' => Type::string(),
                'description' => 'Group'
            ],
            'values' => [
                'name' => 'values',
                'type' => Type::listOf(GraphQL::type('ValueInput')),
                'description' => 'Value'
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Description'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
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
            $key = HelperService::slugify(HelperService::clean($args['description']));
            $isSetting = Setting::where('key', $key)->exists();
            $values = $args['values'];
            $group = HelperService::clean($args['group']);
            $description = HelperService::clean($args['description']);
            $status = (boolean)$args['status'] ?? true;

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-settings')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!GroupType::validate($args['group'])) {
                return new Error(HelperService::message($lang, 'invalid'));
            }elseif($isSetting) {
                return new Error(HelperService::message($lang, 'exists'));
            }

            return Setting::create([
                'key' => $key,
                'group' => $group,
                'values' => $values,
                'description' => $description,
                'status' => $status
            ]);
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
