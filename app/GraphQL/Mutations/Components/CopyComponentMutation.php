<?php

namespace App\GraphQL\Mutations\Components;

use App\Models\Admin;
use App\Models\Component;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Stevebauman\Purify\Facades\Purify;

class CopyComponentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'copyComponent',
        'description' => 'Component Copy'
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
                'description' => 'Token',
            ]
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try {
            $auth = Admin::find(request()->auth['sub']);
            $token = HelperService::clean($args['token']);
            $component = Component::where('token', $token)->with('children')->first();

            if(!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif (!$component) {
                return new Error(HelperService::message($lang, 'found').'- Component');
            }

            $title = $component->title.'_'.time();
            $key = HelperService::slugify($title);

            Component::create([
                'title' => $title,
                'type' => $component->type,
                'key' => $key,
                'content' => $component->content,
                'status' => $component->status,
                'order' => $component->order,
                'section_id' => null,
                'parent_id' => null
            ]);

            $component->load('children');

            return Section::find($component->section_id)->load('components');
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
