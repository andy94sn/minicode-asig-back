<?php

    declare(strict_types=1);

    namespace App\GraphQL\Queries\Posts;

    use App\Models\Admin;
    use App\Models\Category;
    use App\Models\Post;
    use App\Services\HelperService;
    use GraphQL\Error\Error;
    use GraphQL\Type\Definition\ResolveInfo;
    use GraphQL\Type\Definition\Type;
    use Illuminate\Support\Facades\Log;
    use Rebing\GraphQL\Support\Facades\GraphQL;
    use Rebing\GraphQL\Support\Query;
    use Closure;

    class PostsQuery extends Query
    {
        protected $attributes = [
            'name' => 'getPosts',
            'description' => 'Return Posts',
            'model' => Post::class
        ];

        public function type(): Type
        {
            return GraphQL::type('PostPagination');
        }

        public function args(): array
        {
            return [
                'perPage' => [
                    'type' => Type::int(),
                    'description' => 'Paginate'
                ],
                'page' => [
                    'name' => 'page',
                    'type' => Type::int(),
                    'description' => 'Page'
                ],
                'name' => [
                    'name' => 'name',
                    'type' => Type::string(),
                    'description' => 'Name'
                ],
                'orderBy' => [
                    'name' => 'orderBy',
                    'type' => Type::string(),
                    'description' => 'Order By'
                ],
                'sortBy' => [
                    'name' => 'sortBy',
                    'type' => Type::string(),
                    'description' => 'Sort By'
                ],
                'category' => [
                    'name' => 'category',
                    'type' => Type::string(),
                    'description' => 'Category Token'
                ],
            ];
        }

        public function resolve($root, array $args, $context)
        {
            $lang = $args['lang'] ?? 'ro';

            try{
                $auth = Admin::find(request()->auth['sub']);
                $perPage = $args['perPage'] ?? 10;
                $page = $args['page'] ?? 1;

                if (!$auth) {
                    throw new Error(HelperService::message($lang, 'denied'));
                }elseif(!$auth->hasPermissionTo('manage-blog')) {
                    throw new Error(HelperService::message($lang, 'permission'));
                }

                $query = Post::query();

                if (isset($args['name'])) {
                    $query->where('name', 'like', '%' . $args['name'] . '%');
                }

                if (!empty($args['category'])) {
                    $category = Category::where('token', $args['category'])->first();

                    if($category){
                        $query->where('category_id', $category->id);
                    }
                }

                if(!empty($args['orderBy']) && !empty($args['sortBy'])){
                    $query->orderBy($args['sortBy'], $args['orderBy']);
                }else{
                    $query->orderBy('created_at', 'desc');
                }

                $posts = $query->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $posts->items(),
                    'meta' => [
                        'total' => $posts->total(),
                        'current_page' => $posts->currentPage(),
                        'last_page' => $posts->lastPage(),
                        'per_page' => $posts->perPage()
                    ]
                ];
            }catch(\Exception $exception){
                Log::error($exception->getMessage());
                return new Error(HelperService::message($lang, 'error'));
            }
        }
    }
