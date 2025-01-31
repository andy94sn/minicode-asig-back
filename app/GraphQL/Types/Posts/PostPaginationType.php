<?php


    namespace App\GraphQL\Types\Posts;

    use GraphQL\Type\Definition\Type;
    use Rebing\GraphQL\Support\Facades\GraphQL;
    use Rebing\GraphQL\Support\Type as GraphQLType;

    class PostPaginationType extends GraphQLType
    {
        protected $attributes = [
            'name' => 'PostPagination',
            'description' => 'Pagination Posts'
        ];

        public function fields(): array
        {
            return [
                'data' => [
                    'type' => Type::listOf(GraphQL::type('Post')),
                    'description' => 'Posts List'
                ],
                'meta' => [
                    'type' => GraphQL::type('Pagination'),
                    'description' => 'Pagination Info'
                ],
            ];
        }
    }
