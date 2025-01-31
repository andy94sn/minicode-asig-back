<?php

namespace App\GraphQL\Queries\Pages;

use App\Enums\PageType;
use App\Models\Admin;
use App\Models\Page;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PagesQuery extends Query
{
    protected $attributes = [
        'name' => 'getPages',
        'description' => 'Return Pages',
        'model' => Page::class
    ];

    public function type(): Type
    {
        return GraphQL::type('PagePagination');
    }

    public function args(): array
    {
        return [
            'type' => [
                'type' => Type::getNullableType(GraphQL::type('PageEnum')),
                'description' => 'Type'
            ],
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Pagination'
            ],
            'page' => [
                'type' => Type::int(),
                'description' => 'Page number'
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status'
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'title'
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
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Page::query()->select('token', 'slug', 'status', 'title', 'type')
                ->with([
                    'translations:id,page_id,language,title,content'
                ]);

            if (isset($args['title'])) {
                $query->where('title', 'like', '%' . $args['title'] . '%');
            }

            if(isset($args['status'])){
                $query->where('status', $args['status']);
            }

            if($args['type']){
                $query->where('type', $args['type']);
            }

            if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                $query->orderBy($args['sortBy'], $args['orderBy']);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $pages = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => $pages->items(),
                'meta' => [
                    'total' => $pages->total(),
                    'current_page' => $pages->currentPage(),
                    'last_page' => $pages->lastPage(),
                    'per_page' => $pages->perPage(),
                    'from' => $pages->firstItem(),
                    'to' => $pages->lastItem()
                ]
            ];
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
