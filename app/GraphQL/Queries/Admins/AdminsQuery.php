<?php

namespace App\GraphQL\Queries\Admins;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AdminsQuery extends Query
{
    protected $attributes = [
        'name' => 'getAdmins',
        'description' => 'Return Admins',
        'model' => Admin::class
    ];

    public function type(): Type
    {
        return GraphQL::type('AdminPagination');
    }

    public function args(): array
    {
        return [
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Paginate'
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
            $perPage = $args['perPage'] ?? 10;
            $admins = Admin::with('roles')->paginate($perPage);

            foreach($admins as $key => $admin){
                $admins[$key]['role'] = $admin->roles->first();
            }

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            return [
                'data' => $admins->items(),
                'meta' => [
                    'total' => $admins->total(),
                    'current_page' => $admins->currentPage(),
                    'last_page' => $admins->lastPage(),
                    'per_page' => $admins->perPage(),
                ]
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
