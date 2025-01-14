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
        'description' => 'Update Settings'
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'group' => [
                'name' => 'group',
                'type' => Type::string(),
                'description' => 'Group'
            ],
            'settings' => [
                'name' => 'settings',
                'type' => Type::listOf(GraphQL::type('SettingInput')),
                'description' => 'Settings'
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
            $settings = $args['settings'];

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-settings')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!GroupType::validate($args['group'])) {
                return new Error(HelperService::message($lang, 'invalid').'Group');
            }

            if($settings){
                foreach ($settings as $setting){
                    $existSetting = Setting::where('token', HelperService::clean($setting['token']))->first();

                    if($existSetting){
                        $existSetting->update([
                            'values' => $setting['values'],
                            'group' => $group,
                            'status' => $setting['status'],
                        ]);
                    }else{
                        return false;
                    }
                }
            }

            return true;
        }catch (\Exception $exception) {
           Log::info($exception->getMessage());
           return new Error(HelperService::message($lang, 'error'));
        }

    }
}
