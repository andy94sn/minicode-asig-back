<?php

namespace App\GraphQL\Queries\Sections;

use App\Models\Admin;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SectionsQuery extends Query
{
    protected $attributes = [
        'name' => 'getSections',
        'description' => 'Return Sections',
        'model' => Section::class
    ];

    public function type(): Type
    {
        return GraphQL::type('SectionPagination');
    }

    public function args(): array
    {
        return [
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Pagination'
            ],

            'page' => [
                'type' => Type::int(),
                'description' => 'Page number'
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
            }elseif(!$auth->hasPermissionTo('manage-content')){
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Section::select('token', 'slug', 'name', 'order', 'status')->with('components');
            $sections = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => $sections->items(),
                'meta' => [
                    'total' => $sections->total(),
                    'current_page' => $sections->currentPage(),
                    'last_page' => $sections->lastPage(),
                    'per_page' => $sections->perPage(),
                    'from' => $sections->firstItem(),
                    'to' => $sections->lastItem()
                ]
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
