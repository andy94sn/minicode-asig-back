<?php

namespace App\GraphQL\Mutations\Admins;

use App\Mail\PasswordMail;
use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ResetPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'resetPassword',
        'description' => 'Reset Password Admin'
    ];

    public function type(): Type
    {
        return GraphQL::type('Admin');
    }

    public function args(): array
    {
        return [
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Email'
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
            $admin = Admin::where('token', $token)->first();
            $password = Str::random('12');

            if (!$auth && !$auth->is_super) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-admins')) {
                return new Error(HelperService::message($lang, 'permission'));
            }elseif(!$admin){
                return new Error(HelperService::message($lang, 'found').'- Admin');
            }

            $admin->password = Hash::make($password);
            Mail::to($admin->email)->send(new PasswordMail($password));

            return $admin;
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
