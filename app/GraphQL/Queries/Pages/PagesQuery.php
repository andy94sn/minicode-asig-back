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
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';
        $type = $args['type'] ?? null;

        try{
            $auth = Admin::find(request()->auth['sub']);
            $validTypes = PageType::values();
            $perPage = $args['perPage'] ?? 10;

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if($type){
                $type = HelperService::clean($type);
            }

            $query = Page::select('token', 'slug', 'status', 'title', 'type')
                ->with([
                    'translations:id,page_id,language,title,content'
                ]);

            if($type){
                $pages = $query->where('type', $type)->paginate($perPage);
            }else{
                $pages = $query->paginate($perPage);
            }

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
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
