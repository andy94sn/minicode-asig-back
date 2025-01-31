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
            'orderBy' => [
                'type' => Type::string(),
                'description' => 'Order By'
            ],
            'sortBy' => [
                'type' => Type::string(),
                'description' => 'Sort By'
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
            $page = $args['page'] ?? 1;

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }


            $query = Admin::query();
            if (isset($args['name'])) {
                $query->where('name', 'like', '%' . $args['name'] . '%');
            }

            if (isset($args['email'])) {
                $query->where('email', 'like', '%' . $args['email'] . '%');
            }

            if(isset($args['status'])){
                $query->where('status', $args['status']);
            }

            if (isset($args['role'])) {
                $query->join('model_has_roles', 'admins.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', $args['role']);
            }

            if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                $query->orderBy($args['sortBy'], $args['orderBy']);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $admins = $query->paginate($perPage, ['admins.*'], 'page', $page);

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
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
