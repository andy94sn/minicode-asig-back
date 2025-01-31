<?php

    namespace App\GraphQL\Queries;

    use App\Models\Category;
    use App\Models\Page;
    use App\Models\Post;
    use App\Services\HelperService;
    use GraphQL\Error\Error;
    use GraphQL\Type\Definition\NonNull;
    use GraphQL\Type\Definition\Type;
    use Illuminate\Support\Facades\Log;
    use Rebing\GraphQL\Support\Facades\GraphQL;
    use Rebing\GraphQL\Support\Mutation;
    use Exception;

    class BlogQuery extends Mutation
    {

        protected $attributes = [
            'name' => 'getBlog',
            'description' => 'Return Blog With Categories and Posts'
        ];

        public function type(): Type
        {
            return GraphQL::type('Blog');
        }

        public function args(): array
        {
            return [
                'lang' => [
                    'name' => 'lang',
                    'type' => new nonNull(GraphQL::type('EnumLanguage')),
                    'description' => 'Language Page'
                ]
            ];
        }

        public function resolve($root, array $args)
        {
            $lang = $args['lang'];

            try{
                $data = [
                    'categories' => [],
                    'posts' => []
                ];

                $categories = Category::with([
                    'translations' => function ($query) use ($lang) {
                        $query->where('language', $lang);
                    },
                    'posts' => function ($query) {
                        $query->where('status', 'published')->orderBy('order');
                    },
                    'posts.translations' => function ($query) use ($lang) {
                        $query->where('language', $lang);
                    }
                ])->get();

                $posts = Post::whereNull('category_id')
                    ->where('status', 'published')
                    ->with([
                        'translations' => function ($query) use ($lang) {
                            $query->where('language', $lang);
                        }
                    ])
                    ->orderBy('order')
                    ->get();

                $data['categories'] = $categories;
                $data['posts'] = $posts;

                return $data;
            }catch(Exception $exception){
                Log::error($exception->getMessage());
                return new Error($exception->getMessage());
            }
        }
    }
