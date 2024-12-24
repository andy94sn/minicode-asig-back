<?php

namespace App\GraphQL\Queries\Pages;

use App\Models\Admin;
use App\Models\Page;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PageQuery extends Query
{
    protected $attributes = [
        'name' => 'getPage',
        'description' => 'Single Page',
        'model' => Page::class
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
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token'
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
            $token = HelperService::clean($args['token']);
            $page = Page::where('token', $token)->with('sections')->first();

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')){
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$page) {
                return new Error(HelperService::message($lang, 'found').' - Page');
            }

            if($page->type == 'complex'){
                $page->translations = null;
            }

            $page->load('sections');
            return $page;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
