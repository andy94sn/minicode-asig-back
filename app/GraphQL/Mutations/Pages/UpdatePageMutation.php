<?php

namespace App\GraphQL\Mutations\Pages;

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

class UpdatePageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updatePage',
        'description' => 'Update Page',
    ];

    public function type(): Type
    {
        return GraphQL::type('Page');
    }

    public function args(): array
    {
        return [
            'token' => [
                'name' => 'token',
                'type' => new NonNull(Type::string()),
                'description' => 'Token Page'
            ],
            'type' => [
                'name' => 'type',
                'type' => new NonNull(Type::string()),
                'description' => 'Type Page'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::boolean()
            ],
            'translations' => [
                'name' => 'translations',
                'type' => Type::listOf(GraphQL::type('PageTranslationInput')),
                'description' => 'Translations',
            ]
        ];
    }

    /**
     * @throws Error
     */
    protected function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);
            $type = HelperService::clean($args['type']);
            $token = HelperService::clean($args['token']);


            $page = Page::where('token', $token)->first();
            $status = $args['status'] ?? $page->status;
            $translations = $args['translations'] ?? array();

            if(!$page){
                return new Error(HelperService::message($lang, 'found'));
            }elseif(!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(($page->type == 'complex' || $page->type == 'general') && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $page->update([
                'type' => $type ?? null,
                'status' => $status
            ]);

            if (isset($translations) && $page->type === 'simple') {
                foreach ($translations as $item) {
                    $translation = PageTranslation::where('page_id', $page->id)
                        ->where('language', $item['language'])
                        ->first();

                    if ($translation) {
                        $translation->title = $item['title'] ?? $translation->title;
                        $translation->content = $item['content'] ?? $translation->content;
                        $translation->meta_title = $item['meta_title'] ?? $translation->meta_title;
                        $translation->meta_description = $item['meta_description'] ?? $translation->meta_description;
                        $translation->meta_keywords = $item['meta_keywords'] ?? $translation->meta_keywords;
                        $translation->save();
                    } else {
                        $pageTranslation = new PageTranslation();
                        $pageTranslation->page_id = $page->id;
                        $pageTranslation->language = $item['language'];
                        $pageTranslation->title = $item['title'];
                        $pageTranslation->content = $item['content'];
                        $pageTranslation->meta_title = $item['meta_title'];
                        $pageTranslation->meta_description = $item['meta_description'];
                        $pageTranslation->meta_keywords = $item['meta_keywords'];
                        $pageTranslation->save();
                    }
                }
            }

            return $page;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
