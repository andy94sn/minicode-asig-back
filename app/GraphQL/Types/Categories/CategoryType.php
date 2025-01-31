<?php

    namespace App\GraphQL\Types\Categories;

    use App\Models\Category;
    use GraphQL\Type\Definition\Type;
    use Rebing\GraphQL\Support\Facades\GraphQL;
    use Rebing\GraphQL\Support\Type as GraphQLType;

    class CategoryType extends GraphQLType
    {
        protected $attributes = [
            'name' => 'Category',
            'description' => 'Category Type',
            'model' => Category::class
        ];

        public function fields(): array
        {
            return [
                'token' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Token'
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'Name'
                ],
                'slug' => [
                    'type' => Type::string(),
                    'description' => 'Slug'
                ],
                'image' => [
                    'type' => Type::string(),
                    'description' => 'Image'
                ],
                'order' => [
                    'type' => Type::int(),
                    'description' => 'Order'
                ],
                'status' => [
                    'type' => Type::boolean(),
                    'description' => 'Status'
                ],
                'translations' => [
                    'type' => Type::listOf(GraphQL::type('CategoryTranslation')),
                    'description' => 'List Translations'
                ],
                'posts' => [
                    'type' => Type::listOf(GraphQL::type('Post')),
                    'description' => 'List Posts'
                ]
            ];
        }
    }
