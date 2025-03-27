<?php

namespace App\GraphQL\Queries\Admins;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AgentsQuery extends Query
{
    protected $attributes = [
        'name' => 'getAgents',
        'description' => 'Return Agents',
        'model' => Admin::class
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Admin'));
    }

    public function args(): array
    {
        return [];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        // try{
            $auth = Admin::find(request()->auth['sub']);

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-payments')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            $query = Admin::query()->withPayments();
            if (isset($args['name'])) {
                $query->where('name', 'like', '%' . $args['name'] . '%');
            }


            $query->orderBy('created_at', 'desc');
            

            return $query->get();
        // }catch(\Exception $exception){
        //     Log::error($exception->getMessage());
        //     return new Error(HelperService::message($lang, 'error'));
        // }
    }
}
