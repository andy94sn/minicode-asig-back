<?php

namespace App\GraphQL\Queries\Components;

use App\Enums\ComponentType;
use App\Models\Admin;
use App\Services\HelperService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Log;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ComponentFieldsQuery extends Query
{
    protected $attributes = [
        'name' => 'getComponentFields',
        'description' => 'Component Fields'
    ];

    public function type(): Type
    {
        return GraphQL::type('ComponentFields');
    }

    public function args(): array
    {
        return [
            'type' => [
                'type' => GraphQL::type('ComponentEnum'),
                'description' => 'Component Type'
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
            $type = HelperService::clean($args['type']);

            if (!$auth) {
                throw new Error(HelperService::message($lang, 'denied'));
            }elseif(!$auth->hasPermissionTo('manage-content')) {
                throw new Error(HelperService::message($lang, 'permission'));
            }

            return $this->getComponentFields($type);
        }catch(\Exception $exception){
            Log::info($exception->getMessage());
            return new Error(HelperService::message($lang, 'error'));
        }
    }

    /**
     * Return Component Type
     *
     * @param string $type
     * @param array $languages
     * @return array
     * @throws Error
     */
    private function getComponentFields(string $type): array
    {
        $fields = match ($type) {
            ComponentType::TITLE->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'bold' => [
                    'label' => 'Title Bold',
                    'type' => 'checkbox',
                    'required' => true
                ],
                'break' => [
                    'label' => 'Break line',
                    'type' => 'checkbox',
                    'required' => true
                ]
            ],
            ComponentType::TEXT->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'checkbox',
                    'required' => true
                ],
                'editor' => [
                    'label' => 'Editor',
                    'type' => 'checkbox',
                    'required' => true
                ]
            ],
            ComponentType::BUTTON->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'link' => [
                    'label' => 'Link',
                    'type' => 'text',
                    'required' => true
                ],
                'blank' => [
                    'label' => 'New Tab',
                    'type' => 'checkbox',
                    'required' => true
                ]
            ],
            ComponentType::MEDIA->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'background' => [
                    'label' => 'Background Image',
                    'type' => 'checkbox',
                    'required' => true
                ],
                'alt' => [
                    'label' => 'Alt',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            ComponentType::FAQ->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'question' => [
                    'label' => 'Question',
                    'type' => 'textarea',
                    'required' => true
                ],
                'answer' => [
                    'label' => 'Answer',
                    'type' => 'textarea',
                    'required' => true
                ],
                'editor' => [
                    'label' => 'Editor',
                    'type' => 'checkbox',
                    'required' => true
                ]
            ],
            ComponentType::CARD->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'caption' => [
                    'label' => 'Caption',
                    'type' => 'text',
                    'required' => true
                ],
                'icon' => [
                    'label' => 'Icon',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            ComponentType::FORM->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'caption' => [
                    'label' => 'Caption',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            ComponentType::INPUT->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],
                'label' => [
                    'label' => 'Label',
                    'type' => 'text',
                    'required' => true
                ],

                'placeholder' => [
                    'label' => 'Placeholder',
                    'type' => 'text',
                    'required' => true
                ],

                'type' => [
                    'label' => 'Type',
                    'type' => 'text',
                    'required' => true
                ],
            ],
            ComponentType::SELECT->value => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'required' => true
                ],

                'label' => [
                    'label' => 'Label',
                    'type' => 'text',
                    'required' => true
                ],

                'placeholder' => [
                    'label' => 'Placeholder',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            default => throw new Error("Component type not supported"),
        };

        return [
            'status' => true,
            'fields' => $fields
        ];
    }

    /**
     *
     * @param string $type
     * @return bool
     */
    private function shouldUseEditor(string $type): bool
    {
        return in_array($type, [ComponentType::TEXT->value, ComponentType::FAQ->value]);
    }
}
