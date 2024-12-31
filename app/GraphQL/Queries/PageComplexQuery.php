<?php

namespace App\GraphQL\Queries;

use App\Models\Page;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class PageComplexQuery extends Mutation
{

    protected $attributes = [
        'name' => 'getComplexPage',
        'description' => 'Return Page With Sections and Components',
        'model' => Page::class
    ];

    public function type(): Type
    {
        return GraphQL::type('PageComplex');
    }

    public function args(): array
    {
        return [
            'key' => [
                'name' => 'key',
                'type' => new nonNull(GraphQL::type('EnumPage')),
                'description' => 'Key Page'
            ],
            'lang' => [
                'name' => 'lang',
                'type' => new nonNull(GraphQL::type('EnumLanguage')),
                'description' => 'Language Page'
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        try{
            $page = Page::where(['slug' => trim($args['key']), 'type' => 'complex'])
                ->with(['sections' => function ($query) {
                    $query->orderBy('order');
                },'sections.components' => function ($query) {
                    $query->whereNull('parent_id')->orderBy('order');
                }])
                ->first();

            if(!$page){
                return new Error(HelperService::message($args['lang'], 'page'));
            }

            return $page;
        }catch(Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return new Error($exception->getMessage());
        }
    }
}
