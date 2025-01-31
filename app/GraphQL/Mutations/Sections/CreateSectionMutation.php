<?php


namespace App\GraphQL\Mutations\Sections;

use App\Models\Admin;
use App\Models\Page;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateSectionMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createSection',
        'description' => 'Section'
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
                'description' => 'Token Page'
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name'
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::int(),
                'description' => 'Order'
            ],
            'status' => [
                'name' => 'status',
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
            $token = HelperService::clean($args['token']);
            $name = HelperService::clean($args['name']);
            $slug = HelperService::slugify($name);

            $page = Page::with('sections.components')->where('token', $token)->first();
            $section = Section::where(['slug' => $slug])->exists();

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-pages')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$page) {
                return  new Error(HelperService::message($lang, 'found'));
            }elseif($page->type !== 'complex' && $page->type !== 'general'){
                return new Error(HelperService::message($lang, 'invalid'));
            }elseif($section){
                return new Error(HelperService::message($lang, 'exists'));
            }

            Section::create([
                'name' => $name,
                'order' => $args['order'] ?? 1,
                'status' => $args['status'] ?? true,
                'page_id' => $page->id
            ]);

            $page->load('sections');
            return $page;
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
