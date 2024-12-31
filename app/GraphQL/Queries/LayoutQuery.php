<?php

namespace App\GraphQL\Queries;

use App\Models\Page;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Exception;

class LayoutQuery extends Mutation
{

    protected $attributes = [
        'name' => 'getLayout',
        'description' => 'Layout Pages With Sections and Components',
        'model' => Page::class
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('PageComplex'));
    }

    public function args(): array
    {
        return [
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
            return Page::where(['type' => 'general'])
                ->with(['sections' => function ($query) {
                    $query->orderBy('order');
                }, 'sections.components' => function ($query) {
                    $query->whereNull('parent_id')->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
        }catch(Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return new Error($exception->getMessage());
        }
    }
}
