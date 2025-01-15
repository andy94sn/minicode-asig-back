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
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'description' => 'Page'
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'description' => 'Email'
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Name'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'role' => [
                'name' => 'role',
                'type' => Type::string(),
                'description' => 'Role'
            ],
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
            $page = $args['page'] ?? 1;
            $query = Admin::query();

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            if (isset($args['name'])) {
                $query->where('name', 'like', '%' . $args['name'] . '%');
            }
            if (isset($args['email'])) {
                $query->where('email', 'like', '%' . $args['email'] . '%');
            }
            if(isset($args['status'])){
                $query->where('status', $args['status']);
            }

            $admins = $query->paginate($perPage, ['*'], 'page', $page);

            $admins->getCollection()->transform(function ($admin) {
                $admin['role'] = $admin->roles->first();
                return $admin;
            });

            return [
                'data' => $admins->items(),
                'meta' => [
                    'total' => $admins->total(),
                    'current_page' => $admins->currentPage(),
                    'last_page' => $admins->lastPage(),
                    'per_page' => $admins->perPage()
                ]
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
