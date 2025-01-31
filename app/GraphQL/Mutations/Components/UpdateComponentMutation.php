<?php

namespace App\GraphQL\Mutations\Components;

use App\Models\Admin;
use App\Models\Component;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;


class UpdateComponentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateComponent',
        'description' => 'Update Component'
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
                'description' => 'Component Token',
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('TranslationInput')),
                'description' => 'Component Translations',
            ],
            'type' => [
                'name' => 'type',
                'type' => GraphQL::type('ComponentEnum'),
                'description' => 'Component Type'
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
            $component = Component::where('token', $token)->first();
            $section  = $component->section;
            $status = (boolean)$args['status'] ?? true;
            $translations = $args['translations'] ?? [];
            $type = HelperService::clean($args['type']);

            if(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $component->update([
                'type'  => $type,
                'content' => $translations,
                'status' => $status,
                'order' => (int)$args['order'] ?? 1
            ]);

            $section->load('components');
            return $section;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }

}
