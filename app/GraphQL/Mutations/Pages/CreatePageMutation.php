<?php

namespace App\GraphQL\Mutations\Pages;

use App\Enums\LanguageType;
use App\Models\Admin;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreatePageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createPage',
        'description' => 'Create Page',
    ];

    public function type(): Type
    {
        return GraphQL::type('Page');
    }

    public function args(): array
    {
        return [
            'title' => [
                'name' => 'title',
                'type' => new NonNull(Type::string())
            ],
            'type' => [
                'type' => new NonNull(GraphQL::type('PageEnum')),
                'description' => 'Type'
            ],
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status'
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
            $title = HelperService::clean($args['title']);
            $type = HelperService::clean($args['type']);
            $status = $args['status'] ?? true;
            $languages = LanguageType::values();
            $slug = HelperService::slugify($title);
            $page = Page::withTrashed()->where('slug', $slug)->first();

            if($page){
                return new Error(HelperService::message($lang, 'exists'));
            }elseif(!$auth){
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $page = Page::create([
                'title' => $title,
                'type'  => $type,
                'status' => $status
            ]);

            if($page->type === 'simple'){
                foreach ($languages as $language) {
                    $pageTranslation = new PageTranslation();
                    $pageTranslation->page_id = $page->id;
                    $pageTranslation->language = $language;
                    $pageTranslation->save();
                }
            }

            return $page;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
