<?php

namespace App\GraphQL\Queries\Contacts;

use App\Models\Admin;
use App\Models\Contact;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ContactsQuery extends Query
{
    protected $attributes = [
        'name' => 'getContacts',
        'description' => 'Return Contacts',
        'model' => Contact::class
    ];

    public function type(): Type
    {
        return GraphQL::type('ContactPagination');
    }

    public function args(): array
    {
        return [
            'group' => [
                'name' => 'group',
                'type' => Type::string(),
                'description' => 'Group',
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'description' => 'Number Pages',
                'defaultValue' => 10
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1
            ],
            'orderBy' => [
                'type' => Type::string(),
                'description' => 'Order By'
            ],
            'sortBy' => [
                'type' => Type::string(),
                'description' => 'Sort By'
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
            $perPage = $args['perPage'] ?? 10;
            $page = $args['page'] ?? 1;

            if (!$auth) {
                return new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-contacts')) {
                return new Error(HelperService::message($lang, 'permission'));
            }

            $query = Contact::query();

            if (isset($args['group'])) {
                $query->where('page', 'like', '%' . $args['group'] . '%');
            }

            if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                $query->orderBy($args['sortBy'], $args['orderBy']);
            }else{
                $query->orderBy('created_at', 'desc');
            }

            $contacts = $query->paginate($perPage, ['*'], 'page', $page);

            $contacts->getCollection()->transform(function ($contact) {
                $contact->group = $contact->page;
                return $contact;
            });

            return [
                'data' => $contacts->items(),
                'meta' => [
                    'total' => $contacts->total(),
                    'per_page' => $contacts->perPage(),
                    'current_page' => $contacts->currentPage(),
                    'last_page' => $contacts->lastPage(),
                    'from' => $contacts->firstItem(),
                    'to' => $contacts->lastItem()
                ],
            ];
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }
}
