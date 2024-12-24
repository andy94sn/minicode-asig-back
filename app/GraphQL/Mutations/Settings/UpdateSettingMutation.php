<?php
namespace App\GraphQL\Mutations\Settings;

use App\Enums\GroupType;
use App\Models\Admin;
use App\Models\Setting;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateSettingMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateSetting',
        'description' => 'Update Setting'
    ];

    public function type(): Type
    {
        return GraphQL::type('Setting');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::listOf(GraphQL::type('ValueInput')),
                'description' => 'Values'
            ],
            'group' => [
                'name' => 'group',
                'type' => new nonNull(GraphQL::type('GroupEnum')),
                'description' => 'Group'
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
            $token = HelperService::clean($args['token']);
            $group = HelperService::clean($args['group']);
            $setting = Setting::where('token', $token)->first();
            $status = (boolean)$args['status'] ?? true;
            $value = $args['value'] ?? [];

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-settings')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!GroupType::validate($args['group'])) {
                return new Error(HelperService::message($lang, 'invalid').'Group');
            }elseif(!$setting){
                return new Error(HelperService::message($lang, 'found').'Setting');
            }

            $setting->update([
                'value' => $value,
                'group' => $group,
                'status' => $status,
            ]);

            return $setting;
        }catch (\Exception $exception) {
           Log::info($exception->getMessage());
           return new Error(HelperService::message($lang, 'error'));
        }

    }
}
