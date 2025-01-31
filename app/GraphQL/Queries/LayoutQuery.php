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

class LayoutQuery extends Mutation
{

    protected $attributes = [
        'name' => 'getLayout',
        'description' => 'Layout Pages (Header and Footer) With Sections and Components',
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
        $lang = $args['lang'];

        try{
            return Page::where(['type' => 'general'])
                ->with(['sections' => function ($query) {
                    $query->orderBy('order');
                }, 'sections.components' => function ($query) {
                    $query->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
        }catch(Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
