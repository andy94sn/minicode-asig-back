<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Categories;
use App\Models\Admin;
use App\Models\Category;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CategoriesQuery extends Query
{
    protected $attributes = [
        'name' => 'getCategories',
        'description' => 'Return Categories Blog Page',
        'model' => Category::class
    ];

    public function type(): Type
    {
        return GraphQL::type('CategoryPagination');
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
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Name'
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

    public function resolve($root, array $args, $context)
    {
        try{
            $lang = $args['lang'] ?? 'ro';
            $auth = Admin::find(request()->auth['sub']);
            $perPage = $args['perPage'] ?? 10;
            $page = $args['page'] ?? 1;

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-blog')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            $query = Category::query();

            if(!empty($args['name'])){
                $query->where('name', 'like', '%' . $args['name'] . '%');
            }

            if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                $query->orderBy($args['sortBy'], $args['orderBy']);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $categories = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => $categories->items(),
                'meta' => [
                    'total' => $categories->total(),
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage()
                ]
            ];
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
