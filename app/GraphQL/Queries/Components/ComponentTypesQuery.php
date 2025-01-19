<?php

namespace App\GraphQL\Queries\Components;

use App\Enums\ComponentType;
use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ComponentTypesQuery extends Query
{
    protected $attributes = [
        'name' => 'getComponentTypes',
        'description' => 'Component Types'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('String'));
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);
            $types = ComponentType::values();

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            return $types;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
