<?php


    namespace App\GraphQL\Types\Categories;

    use GraphQL\Type\Definition\Type;
    use Rebing\GraphQL\Support\Facades\GraphQL;
    use Rebing\GraphQL\Support\Type as GraphQLType;

    class CategoryPaginationType extends GraphQLType
    {
        protected $attributes = [
            'name' => 'CategoryPagination',
            'description' => 'Pagination Categories'
        ];

        public function fields(): array
        {
            return [
                'data' => [
                    'type' => Type::listOf(GraphQL::type('Category')),
                    'description' => 'Category List'
                ],
                'meta' => [
                    'type' => GraphQL::type('Pagination'),
                    'description' => 'Pagination Info'
                ],
            ];
        }
    }
