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
            ],
            'components' => [
                'name' => 'components',
                'type' => Type::listOf(GraphQL::type('ComponentInput')),
                'description' => 'Component Input',
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
            $children = array();

            if(!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if(isset($args['components']) && is_array($args['components'])) {
                foreach ($args['components'] as $nestedComponent) {
                    $childComponent = Component::where(['token' => $nestedComponent['token'], 'parent_id' => null])->first();
                    if (!$childComponent) {
                        return new Error(HelperService::message($lang, 'found').' - Component');
                    }else{
                        $children[] = $childComponent;
                    }
                }
            }

            $component->update([
                'type'  => $type,
                'content' => $translations,
                'status' => $status,
                'order' => (integer)$args['order'] ?? 1
            ]);

            foreach($children as $child){
                $child->parent_id = $component->id;
                $child->save();
            }

            $section->load('components');
            return $section;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }

}
