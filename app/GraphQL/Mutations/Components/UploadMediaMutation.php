<?php

namespace App\GraphQL\Mutations\Components;

use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UploadMediaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'uploadMedia',
        'description' => 'Upload Media'
    ];

    public function type(): Type
    {
        return GraphQL::type('UploadResponse');
    }

    public function args(): array
    {
        return [
            'file' => [
                'name' => 'file',
                'type' => GraphQL::type('Upload'),
                'description' => 'Upload File',
                'rules' => ['file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
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
            $file = $args['file'];
            Log::info(print_r($file, true));

            $validator = Validator::make(['file' => $file], [
                'file' => 'required|file|mimes:jpeg,jpg,png,gif|max:2048'
            ]);

            if(!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            if ($validator->fails()) {
                return new Error(HelperService::message($lang, 'invalid'));
            }


            $filename = time().'-'.$file->getClientOriginalName();
            $path = Storage::disk('public')->putFileAs('', $file, $filename);

            return [
                'path' => 'uploads/'.$path,
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
