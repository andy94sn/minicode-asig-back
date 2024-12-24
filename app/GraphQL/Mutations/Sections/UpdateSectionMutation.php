<?php


namespace App\GraphQL\Mutations\Sections;

use App\Models\Admin;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateSectionMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateSection',
        'description' => 'Update Section'
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
            $section = Section::where('token', trim($args['token']))->first();
            $page = $section->page;

            if (!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$section){
                return new Error(HelperService::message($lang, 'found').'Section');
            }elseif(!$page) {
                return new Error(HelperService::message($lang, 'found').'Page');
            }elseif ($page->type !== 'complex'){
                return new Error(HelperService::message($lang, 'invalid'));
            }

            $section->update([
                'order' => $args['order'] ?? $section->order,
                'status' => $args['status'] ?? $section->status
            ]);

            return $page;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
