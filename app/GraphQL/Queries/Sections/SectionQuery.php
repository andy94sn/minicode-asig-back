<?php

namespace App\GraphQL\Queries\Sections;

use App\Models\Admin;
use App\Models\Section;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SectionQuery extends Query
{
    protected $attributes = [
        'name' => 'getSection',
        'description' => 'Single Section',
        'model' => Section::class
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
            $section = Section::where('token', $token)->with('components')->first();

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')){
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$section){
                return new Error(HelperService::message($lang, 'found').' - Page');
            }

            $section->load('components');
            return $section;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }

    }
}
