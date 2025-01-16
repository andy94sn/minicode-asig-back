<?php

    namespace App\GraphQL\Types;

    use GraphQL\Type\Definition\Type;
    use Rebing\GraphQL\Support\Type as GraphQLType;

    class DownloadResponseType extends GraphQLType
    {
        protected $attributes = [
            'name' => 'DownloadResponse',
            'description' => 'Download Response Type'
        ];

        public function fields(): array
        {
            return [
                'fileContent' => [
                    'type' => Type::string(),
                    'description' => 'Content File'
                ],
                'fileName' => [
                    'type' => Type::string(),
                    'description' => 'Name File'
                ],
                'mimeType' => [
                    'type' => Type::string(),
                    'description' => 'MIME TYPE'
                ]
            ];
        }
    }
