<?php


namespace App\GraphQL\Queries\Settings;

use App\Enums\GroupType;
use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;

class GroupsQuery extends Query
{
    protected $attributes = [
        'name' => 'getGroups',
        'description' => 'Return Groups Settings'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
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

            return GroupType::values();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
