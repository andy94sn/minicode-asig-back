<?php

namespace App\GraphQL\Mutations\Components;

use App\Models\Admin;
use App\Models\Component;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;


class CreateComponentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createComponent',
        'description' => 'Create Component'
    ];

    public function type(): Type
    {
        return GraphQL::type('Section');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Section Component Token',
            ],
            'title' => [
                'name' => 'title',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title Component',
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('TranslationInput')),
                'description' => 'Component Translations',
            ],
            'type' => [
                'name' => 'type',
                'type' => GraphQL::type('ComponentEnum'),
                'description' => 'Component Type',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean(),
                'description' => 'Component Status'
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Component Order'
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
            $token = HelperService::clean($args['token']);
            $title = HelperService::clean($args['title']);
            $key = HelperService::slugify($title);

            $section = Section::where('token', $token)->with('components')->first();
            $component = Component::where(['key' => $key, 'section_id' => $section->id])->exists();

            if(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif($component) {
                return new Error(HelperService::message($lang, 'exists'));
            }

            Component::create([
                'title' => $args['title'],
                'type' => $args['type'],
                'key' => $key,
                'content' => $args['translations'] ?? [],
                'status' => $args['status'] ?? true,
                'order' => $args['order'] ?? 1,
                'section_id' => $section->id
            ]);

            $section->load('components');
            return $section;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
