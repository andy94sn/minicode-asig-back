<?php

declare(strict_types = 1);

    use App\GraphQL\Mutations\Admins\CreateAdminMutation;
use App\GraphQL\Mutations\Admins\CreatePaymentMutation;
use App\GraphQL\Mutations\Admins\DeleteAdminMutation;
    use App\GraphQL\Mutations\Admins\DeleteProfileMutation;
    use App\GraphQL\Mutations\Admins\LoginAdminMutation;
    use App\GraphQL\Mutations\Admins\RefreshTokenMutation;
    use App\GraphQL\Mutations\Admins\UpdateAdminMutation;
    use App\GraphQL\Mutations\Admins\UpdateProfileMutation;
    use App\GraphQL\Mutations\Categories\CreateCategoryMutation;
    use App\GraphQL\Mutations\Categories\DeleteCategoryMutation;
    use App\GraphQL\Mutations\Categories\UpdateCategoryMutation;
    use App\GraphQL\Mutations\Components\CreateComponentMutation;
    use App\GraphQL\Mutations\Components\DeleteComponentMutation;
    use App\GraphQL\Mutations\Components\UpdateComponentMutation;
    use App\GraphQL\Mutations\Contacts\CreateContactMutation;
    use App\GraphQL\Mutations\Contacts\DeleteContactMutation;
    use App\GraphQL\Mutations\Media\UploadMediaMutation;
    use App\GraphQL\Mutations\Orders\CreateOrderMutation;
    use App\GraphQL\Mutations\Orders\DeleteOrderMutation;
    use App\GraphQL\Mutations\Orders\DownloadFileMutation;
    use App\GraphQL\Mutations\Pages\CreatePageMutation;
    use App\GraphQL\Mutations\Pages\DeletePageMutation;
    use App\GraphQL\Mutations\Pages\UpdatePageMutation;
    use App\GraphQL\Mutations\Payment\CreateTransactionMutation;
    use App\GraphQL\Mutations\Payment\RefundTransactionMutation;
    use App\GraphQL\Mutations\Posts\CreatePostMutation;
    use App\GraphQL\Mutations\Posts\DeletePostMutation;
    use App\GraphQL\Mutations\Posts\UpdatePostMutation;
    use App\GraphQL\Mutations\Rca\CreateCalculateMutation;
    use App\GraphQL\Mutations\Rca\CreateDocumentMutation;
    use App\GraphQL\Mutations\Roles\CreateRoleMutation;
    use App\GraphQL\Mutations\Roles\DeleteRoleMutation;
    use App\GraphQL\Mutations\Roles\UpdateRoleMutation;
    use App\GraphQL\Mutations\Sections\CreateSectionMutation;
    use App\GraphQL\Mutations\Sections\DeleteSectionMutation;
    use App\GraphQL\Mutations\Sections\UpdateSectionMutation;
    use App\GraphQL\Mutations\Settings\CreateSettingMutation;
    use App\GraphQL\Mutations\Settings\DeleteSettingMutation;
    use App\GraphQL\Mutations\Settings\UpdateSettingMutation;
    use App\GraphQL\Queries\Admins\AdminQuery;
    use App\GraphQL\Queries\Admins\AdminsQuery;
use App\GraphQL\Queries\Admins\AgentsQuery;
use App\GraphQL\Queries\Admins\ProfileQuery;
    use App\GraphQL\Queries\BlogQuery;
    use App\GraphQL\Queries\Categories\CategoriesQuery;
    use App\GraphQL\Queries\Categories\CategoryQuery;
    use App\GraphQL\Queries\Components\ComponentFieldsQuery;
    use App\GraphQL\Queries\Components\ComponentTypesQuery;
    use App\GraphQL\Queries\Contacts\ContactQuery;
    use App\GraphQL\Queries\Contacts\ContactsQuery;
    use App\GraphQL\Queries\Languages\LanguagesQuery;
    use App\GraphQL\Queries\LayoutQuery;
    use App\GraphQL\Queries\ModesInsuranceQuery;
use App\GraphQL\Queries\OrderPaymentQuery;
// use App\GraphQL\Queries\OrderPaymentQuery;
use App\GraphQL\Queries\Orders\OrderQuery;
    use App\GraphQL\Queries\Orders\OrdersQuery;
    use App\GraphQL\Queries\OrderStatusQuery;
    use App\GraphQL\Queries\PageComplexQuery;
    use App\GraphQL\Queries\Pages\PageQuery;
    use App\GraphQL\Queries\Pages\PagesQuery;
    use App\GraphQL\Queries\Pages\PageTypesQuery;
use App\GraphQL\Queries\PaymentLinks\PaymentLinkQuery;
use App\GraphQL\Queries\PaymentLinks\PaymentLinksQuery;
use App\GraphQL\Queries\Permissions\AuthPermissionsQuery;
    use App\GraphQL\Queries\Permissions\PermissionQuery;
    use App\GraphQL\Queries\PersonsQuery;
    use App\GraphQL\Queries\PossessionsInsuranceQuery;
    use App\GraphQL\Queries\Posts\PostQuery;
    use App\GraphQL\Queries\Posts\PostsQuery;
    use App\GraphQL\Queries\Roles\RolesQuery;
    use App\GraphQL\Queries\Sections\SectionQuery;
    use App\GraphQL\Queries\Settings\GroupsQuery;
    use App\GraphQL\Queries\Settings\SettingsGroupsQuery;
    use App\GraphQL\Queries\Settings\SettingsQuery;
    use App\GraphQL\Queries\TermsInsuranceQuery;
    use App\GraphQL\Queries\ZonesInsuranceQuery;
    use App\GraphQL\Types\Admins\AdminDeleteType;
    use App\GraphQL\Types\Admins\AdminPaginationType;
    use App\GraphQL\Types\Admins\AdminResponseType;
    use App\GraphQL\Types\Admins\AdminType;
    use App\GraphQL\Types\BlogType;
    use App\GraphQL\Types\Categories\CategoryDeleteType;
    use App\GraphQL\Types\Categories\CategoryPaginationType;
    use App\GraphQL\Types\Categories\CategoryTranslationInputType;
    use App\GraphQL\Types\Categories\CategoryTranslationType;
    use App\GraphQL\Types\Categories\CategoryType;
    use App\GraphQL\Types\ComponentComplexType;
    use App\GraphQL\Types\Components\ComponentFieldsType;
    use App\GraphQL\Types\Components\ComponentFieldType;
    use App\GraphQL\Types\Components\ComponentInputType;
    use App\GraphQL\Types\Components\ComponentType;
    use App\GraphQL\Types\Components\FieldType;
    use App\GraphQL\Types\Components\TranslationInputType;
    use App\GraphQL\Types\Components\TranslationType;
    use App\GraphQL\Types\Components\UploadResponseType;
    use App\GraphQL\Types\Contacts\ContactDeleteType;
    use App\GraphQL\Types\Contacts\ContactPaginationType;
    use App\GraphQL\Types\Contacts\ContactResponseType;
    use App\GraphQL\Types\Contacts\ContactType;
    use App\GraphQL\Types\Enums\ComponentEnumType;
    use App\GraphQL\Types\Enums\ContactEnumType;
    use App\GraphQL\Types\Enums\EnumInsuranceType;
    use App\GraphQL\Types\Enums\EnumLanguageType;
    use App\GraphQL\Types\Enums\EnumPageType;
    use App\GraphQL\Types\Enums\GroupEnumType;
    use App\GraphQL\Types\Enums\InsuranceEnumType;
    use App\GraphQL\Types\Enums\LanguageEnumType;
    use App\GraphQL\Types\Enums\PageEnumType;
    use App\GraphQL\Types\Enums\TranslationEnumType;
    use App\GraphQL\Types\Errors\ErrorType;
    use App\GraphQL\Types\ModeType;
    use App\GraphQL\Types\Orders\DownloadResponseType;
    use App\GraphQL\Types\Orders\OrderInfoType;
    use App\GraphQL\Types\Orders\OrderPaginationType;
    use App\GraphQL\Types\Orders\OrderResponseType;
    use App\GraphQL\Types\Orders\OrderType;
    use App\GraphQL\Types\PageComplexType;
    use App\GraphQL\Types\Pages\PageDeleteType;
    use App\GraphQL\Types\Pages\PagePaginationType;
    use App\GraphQL\Types\Pages\PageResponseType;
    use App\GraphQL\Types\Pages\PageTranslationInputType;
    use App\GraphQL\Types\Pages\PageTranslationType;
    use App\GraphQL\Types\Pages\PageType;
    use App\GraphQL\Types\Pagination\PaginationType;
    use App\GraphQL\Types\Payment\TransactionResponseType;
    use App\GraphQL\Types\Payment\TransactionType;
    use App\GraphQL\Types\Permissions\PermissionInputType;
    use App\GraphQL\Types\Permissions\PermissionType;
    use App\GraphQL\Types\PersonType;
    use App\GraphQL\Types\PossessionType;
    use App\GraphQL\Types\Posts\PostDeleteType;
    use App\GraphQL\Types\Posts\PostPaginationType;
    use App\GraphQL\Types\Posts\PostTranslationInputType;
    use App\GraphQL\Types\Posts\PostTranslationType;
    use App\GraphQL\Types\Posts\PostType;
    use App\GraphQL\Types\Posts\TagInputType;
    use App\GraphQL\Types\Posts\TagType;
    use App\GraphQL\Types\Rca\CalculateType;
    use App\GraphQL\Types\Rca\DocumentType;
    use App\GraphQL\Types\Roles\RoleType;
    use App\GraphQL\Types\SectionComplexType;
    use App\GraphQL\Types\Sections\SectionPaginationType;
    use App\GraphQL\Types\Sections\SectionType;
    use App\GraphQL\Types\Settings\SettingInputType;
    use App\GraphQL\Types\Settings\SettingPaginationType;
    use App\GraphQL\Types\Settings\SettingType;
    use App\GraphQL\Types\Settings\ValueInputType;
    use App\GraphQL\Types\Settings\ValueResponseType;
    use App\GraphQL\Types\TermType;
    use App\GraphQL\Types\ZoneType;
    use App\Http\Middleware\Authorization;
    use App\Http\Middleware\JwtAuth;
    use Rebing\GraphQL\Support\UploadType;

    return [
    'route' => [
        'prefix' => 'graphql',
        'controller' => Rebing\GraphQL\GraphQLController::class . '@query',
        'middleware' => [],
        'group_attributes' => [],
    ],

    'default_schema' => 'default',

    'batching' => [
        'enable' => true,
    ],

    'schemas' => [
        'default' => [
            'query' => [
                'getLanguages'   => LanguagesQuery::class,
                'statusOrder'    => OrderStatusQuery::class,
                'getTerms'       => TermsInsuranceQuery::class,
                'getZones'       => ZonesInsuranceQuery::class,
                'getModes'       => ModesInsuranceQuery::class,
                'getPossessions' => PossessionsInsuranceQuery::class,
                'getPersons'     => PersonsQuery::class,
                'getPage'        => PageComplexQuery::class,
                'getLayout'      => LayoutQuery::class,
                'getOrderPayment'   => OrderPaymentQuery::class,
                'getSettingsGroups' => SettingsGroupsQuery::class,
                'getBlog'        => BlogQuery::class,
            ],
            'mutation' => [
                'loginAdmin' => LoginAdminMutation::class,
                'refreshToken' => RefreshTokenMutation::class,
                'createContact' => CreateContactMutation::class,
                'createOrder' => CreateOrderMutation::class,
                'calculateMutation'  => CreateCalculateMutation::class,
                'documentMutation'   => CreateDocumentMutation::class,
                'transactionMutation' => CreateTransactionMutation::class,
                'downloadFileMutation' => DownloadFileMutation::class
            ],
            'types' => [
                'AdminResponse'        => AdminResponseType::class,
                'Admin'                => AdminType::class,
                'Role'                 => RoleType::class,
                'EnumPage'             => EnumPageType::class,
                'EnumInsurance'        => EnumInsuranceType::class,
                'EnumLanguage'         => EnumLanguageType::class,
                'ContactResponse'      => ContactResponseType::class,
                'OrderResponse'        => OrderResponseType::class,
                'Order'                 => OrderType::class,
                'OrderInfo'             => OrderInfoType::class,
                'DownloadResponse'     => DownloadResponseType::class,
                'Permission'           => PermissionType::class,
                'Person'               => PersonType::class,
                'Possession'           => PossessionType::class,
                'Translation'          => TranslationType::class,
                'PageComplex'          => PageComplexType::class,
                'PageTranslation'      => PageTranslationType::class,
                'SectionComplex'       => SectionComplexType::class,
                'ComponentComplex'     => ComponentComplexType::class,
                'Term'                 => TermType::class,
                'Zone'                 => ZoneType::class,
                'Mode'                 => ModeType::class,
                'Setting'              => SettingType::class,
                'ValueResponse'        => ValueResponseType::class,
                'Category'             => CategoryType::class,
                'CategoryTranslation'  => CategoryTranslationType::class,
                'Post'                 => PostType::class,
                'PostTranslation'      => PostTranslationType::class,
                'Blog'                 => BlogType::class,
                'Tag'                  => TagType::class,

                //Rca
                'Calculate'    => CalculateType::class,
                'Document'     => DocumentType::class,
                // End
                
                //Payment
                'Transaction'           => TransactionType::class,
                'TransactionResponse'   => TransactionResponseType::class
                // End
            ],
            'middleware' => [
                Authorization::class
            ],

            'method' => ['GET', 'POST'],
            'execution_middleware' => null
        ],
        'user' => [
            'query' => [
                'getAdmins'      => AdminsQuery::class,
                'getAgents'      => AgentsQuery::class,
                'getAdmin'       => AdminQuery::class,
                'getProfile'     => ProfileQuery::class,
                'getPages'       => PagesQuery::class,
                'getPage'        => PageQuery::class,
                'getComponentFields' => ComponentFieldsQuery::class,
                'getRoles'       => RolesQuery::class,
                'getPermissions' => PermissionQuery::class,
                'getContacts'    => ContactsQuery::class,
                'getSettings'    => SettingsQuery::class,
                'getOrders'      => OrdersQuery::class,
                'getPayments'    => PaymentLinksQuery::class,
                'getGroups'      => GroupsQuery::class,
                'getPageTypes'   => PageTypesQuery::class,
                'getOrder'       => OrderQuery::class,
                'getPayment'     => PaymentLinkQuery::class,
                'getContact'     => ContactQuery::class,
                'getSection'     => SectionQuery::class,
                'getComponentTypes' => ComponentTypesQuery::class,
                'getTerms'       => TermsInsuranceQuery::class,
                'getZones'       => ZonesInsuranceQuery::class,
                'getModes'       => ModesInsuranceQuery::class,
                'getPossessions' => PossessionsInsuranceQuery::class,
                'getCategories'  => CategoriesQuery::class,
		        'getCategory'    => CategoryQuery::class,
                'getPosts'       => PostsQuery::class,
		        'getPost'        => PostQuery::class,
                'getAuthPermissions' => AuthPermissionsQuery::class,
                'getLanguages'   => LanguagesQuery::class,
            ],

            'mutation' => [
                'createPage'      => CreatePageMutation::class,
                'updatePage'      => UpdatePageMutation::class,
                'deletePage'      => DeletePageMutation::class,
                'createSection'   => CreateSectionMutation::class,
                'createComponent' => CreateComponentMutation::class,
                'updateComponent' => UpdateComponentMutation::class,
                'deleteComponent' => DeleteComponentMutation::class,
                'updateSection'   => UpdateSectionMutation::class,
                'deleteSection'   => DeleteSectionMutation::class,
                'deleteContact'   => DeleteContactMutation::class,
                'createAdmin'     => CreateAdminMutation::class,
                'updateAdmin'     => UpdateAdminMutation::class,
                'updateProfile'   => UpdateProfileMutation::class,
                'createPayment'   => CreatePaymentMutation::class,
                'createRole'      => CreateRoleMutation::class,
                'deleteRole'      => DeleteRoleMutation::class,
                'updateRole'      => UpdateRoleMutation::class,
                'uploadMedia'     => UploadMediaMutation::class,
                'createSetting'   => CreateSettingMutation::class,
                'updateSetting'   => UpdateSettingMutation::class,
                'deleteSetting'   => DeleteSettingMutation::class,
                'deleteOrder'     => DeleteOrderMutation::class,
                'deleteAdmin'     => DeleteAdminMutation::class,
                'deleteProfile'   => DeleteProfileMutation::class,
                'refundMutation'  => RefundTransactionMutation::class,
                'createCategory'  => CreateCategoryMutation::class,
		        'deleteCategory'  => DeleteCategoryMutation::class,
		        'updateCategory'  => UpdateCategoryMutation::class,
		        'createPost'      => CreatePostMutation::class,
                'deletePost'      => DeletePostMutation::class,
                'updatePost'      => UpdatePostMutation::class,

                //RCA
                'createOrder'       => CreateOrderMutation::class,
                'calculateMutation' => CreateCalculateMutation::class,
                'documentMutation'  => CreateDocumentMutation::class,
            ],

            'types' => [
                'LanguageEnum'    => LanguageEnumType::class,
                'InsuranceEnum'   => InsuranceEnumType::class,
                'TranslationEnum' => TranslationEnumType::class,
                'ComponentEnum'   => ComponentEnumType::class,
                'GroupEnum'       => GroupEnumType::class,
                'PageEnum'        => PageEnumType::class,
                'EnumInsurance'   => EnumInsuranceType::class,
                'AdminResponse'   => AdminResponseType::class,
                'Admin'           => AdminType::class,
                'AdminDelete'     => AdminDeleteType::class,
                'Role'            => RoleType::class,
                'Permission'      => PermissionType::class,
                'Page'            => PageType::class,
                'PageDelete'      => PageDeleteType::class,
                'PageTranslation'      => PageTranslationType::class,
                'PageTranslationInput' => PageTranslationInputType::class,
                'PageResponse'      => PageResponseType::class,
                'Section'           => SectionType::class,
                'SectionPagination' => SectionPaginationType::class,
                'Component'         => ComponentType::class,
                'ComponentFields'   => ComponentFieldsType::class,
                'ComponentField'    => ComponentFieldType::class,
                'Translation'       => TranslationType::class,
                'TranslationInput'  => TranslationInputType::class,
                'ComponentInput'    => ComponentInputType::class,
                'PermissionInput'   => PermissionInputType::class,
                'Field'             => FieldType::class,
                'Contact'           => ContactType::class,
                'ContactDelete'     => ContactDeleteType::class,
                'ContactPagination' => ContactPaginationType::class,
                'AdminPagination'   => AdminPaginationType::class,
                'ContactEnum'       => ContactEnumType::class,
                'Pagination'        => PaginationType::class,
                'OrderPagination'   => OrderPaginationType::class,
                'SettingPagination' => SettingPaginationType::class,
                'SettingInput'      => SettingInputType::class,
                'PagePagination'    => PagePaginationType::class,
                'Order'             => OrderType::class,
                'OrderInfo'         => OrderInfoType::class,
                'Upload'            => UploadType::class,
                'UploadResponse'    => UploadResponseType::class,
                'ValueInput'        => ValueInputType::class,
                'ValueResponse'     => ValueResponseType::class,
                'Setting'           => SettingType::class,
                'Error'             => ErrorType::class,
                'Term'              => TermType::class,
                'Zone'              => ZoneType::class,
                'Mode'              => ModeType::class,
                'Possession'        => PossessionType::class,
                'Category'          => CategoryType::class,
		        'CategoryPagination'   => CategoryPaginationType::class,
                'PostPagination'       => PostPaginationType::class,
		        'CategoryDelete'       => CategoryDeleteType::class,
		        'Post'                 => PostType::class,
		        'PostDelete'           => PostDeleteType::class,
		        'PostTranslation'      => PostTranslationType::class,
		        'CategoryTranslation'  => CategoryTranslationType::class,
		        'CategoryTranslationInput'  => CategoryTranslationInputType::class,
		        'PostTranslationInput'  => PostTranslationInputType::class,
                'TagInput'              => TagInputType::class,
                'Tag'                   => TagType::class,
                'OrderResponse'        => OrderResponseType::class,

                //Rca
                'Calculate'    => CalculateType::class,
                'Document'     => DocumentType::class,
                // End
            ],

            'middleware' => [
                Authorization::class,
                JwtAuth::class
            ],

            'method' => ['GET', 'POST'],

            'execution_middleware' => null,
        ]
    ],

    //'error_formatter' => [Rebing\GraphQL\GraphQL::class, 'formatError'],

    'error_formatter' => function (array $error) {
        return [
            'message' => $error['message'],
        ];
    },


    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    //'errors_handler' => [Rebing\GraphQL\GraphQL::class, 'handleErrors'],

    'errors_handler' => function ($errors, $formatter) {
        return array_map(function ($error) {
            if ($error instanceof \GraphQL\Error\Error) {
                return [
                    'message' => $error->getMessage(),
                ];
            }
            return $error;
        }, $errors);
    },

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://webonyx.github.io/graphql-php/security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity' => null,
        'query_max_depth' => null,
        'disable_introspection' => false,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => Rebing\GraphQL\Support\PaginationType::class,

    /*
     * You can define your own simple pagination type.
     * Reference \Rebing\GraphQL\Support\SimplePaginationType::class
     */
    'simple_pagination_type' => Rebing\GraphQL\Support\SimplePaginationType::class,

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,

    /*
     * Automatic Persisted Queries (APQ)
     * See https://www.apollographql.com/docs/apollo-server/performance/apq/
     *
     * Note 1: this requires the `AutomaticPersistedQueriesMiddleware` being enabled
     *
     * Note 2: even if APQ is disabled per configuration and, according to the "APQ specs" (see above),
     *         to return a correct response in case it's not enabled, the middleware needs to be active.
     *         Of course if you know you do not have a need for APQ, feel free to remove the middleware completely.
     */
    'apq' => [
        // Enable/Disable APQ - See https://www.apollographql.com/docs/apollo-server/performance/apq/#disabling-apq
        'enable' => env('GRAPHQL_APQ_ENABLE', false),

        // The cache driver used for APQ
        'cache_driver' => env('GRAPHQL_APQ_CACHE_DRIVER', config('cache.default')),

        // The cache prefix
        'cache_prefix' => config('cache.prefix') . ':graphql.apq',

        // The cache ttl in seconds - See https://www.apollographql.com/docs/apollo-server/performance/apq/#adjusting-cache-time-to-live-ttl
        'cache_ttl' => 300,
    ],

    /*
     * Execution middlewares
     */
    'execution_middleware' => [
        Rebing\GraphQL\Support\ExecutionMiddleware\ValidateOperationParamsMiddleware::class,
        // AutomaticPersistedQueriesMiddleware listed even if APQ is disabled, see the docs for the `'apq'` configuration
        Rebing\GraphQL\Support\ExecutionMiddleware\AutomaticPersistedQueriesMiddleware::class,
        Rebing\GraphQL\Support\ExecutionMiddleware\AddAuthUserContextValueMiddleware::class,
        // \Rebing\GraphQL\Support\ExecutionMiddleware\UnusedVariablesMiddleware::class,
    ],

    /*
     * Globally registered ResolverMiddleware
     */
    'resolver_middleware_append' => null,

    'playground' => [
        'enabled' => env('GRAPHQL_PLAYGROUND', true),
        'path' => '/graphql-playground'
    ],
];
