<?php

namespace App\GraphQL\Mutations\Contacts;

use App\Enums\PageType;
use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\Page;
use App\Models\Setting;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use App\Models\Order;
use Exception;

class CreateContactMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createContact',
        'description' => 'Create Contact Form',
        'model' => Order::class
    ];

    public function type(): Type
    {
        return GraphQL::type('ContactResponse');
    }

    public function args(): array
    {
        return[
            'page' => [
                'name' => 'page',
                'type' => new nonNull(GraphQL::type('EnumPage')),
                'description' => 'Contact Page'
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Contact Name'
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
                'description' => 'Contact Phone'
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'description' => 'Contact Email'
            ],
            'message' => [
                'name' => 'message',
                'type' => Type::string(),
                'description' => 'Contact Message'
            ],
            'lang' => [
                'name' => 'lang',
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $lang = trim($args['lang']);

        try {
            $page = Page::where('slug', $args['page'])->first();
            $pages = PageType::values();
            $setting = Setting::where('group', 'admin')->first();
            $arrayAdmins = array();

            if($setting){
                $arrayAdmins = $setting->value;
            }

            if(!$page){
                return new Error(HelperService::message($args['lang'], 'page'));
            }

            if(!in_array($page->slug, $pages)){
                return new Error(HelperService::message($args['lang'], 'invalid'));
            }

            $contact = Contact::create([
                 'name' => trim($args['name']),
                 'phone' => trim($args['phone']),
                 'email' => $args['email'] ?? null,
                 'page' => $page->slug,
                 'message' => $args['message'] ?? null
            ]);

            foreach($arrayAdmins as $admin){
                Mail::to($admin)->send(new ContactMail($page));
            }

            if($contact){
                return [
                    'status' => true
                ];
            }

            return [
                'status' => false
            ];
        } catch (Exception $exception){
            Log::error($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
