<?php


namespace App\GraphQL\Queries\Pages;

use App\Enums\PageType;
use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;

class PageTypesQuery extends Query
{
    protected $attributes = [
        'name' => 'pageTypes',
        'description' => 'Return Pages Types'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
    }

    /**
     * @throws Error
     */
    public function resolve($root, $args)
    {
        $lang = $args['lang'] ?? 'ro';

        try{
            $auth = Admin::find(request()->auth['sub']);

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }

            return PageType::values();
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
