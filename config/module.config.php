<?php

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'system_tags'            => [
        'image' => 'media.image',
        'video' => 'media.video',
        'cms.media',
        'cms.media.image',
        'cms.media.video',
    ],
    'service_manager'        => [
        'factories' => [
            \Tag\Service\TagService::class => \Tag\Service\TagServiceFactory::class,
        ],
    ],
    'listeners'              => [
        0 => \Tag\Service\TagService::class
    ],
    'doctrine' => [
        'driver' => [
            'Doctrine_driver' => [
                'paths' => [
                    2 => __DIR__ . '/../src/V1/Rest/Tag',
                ],
            ],
            'orm_default'     => [
                'drivers' => [
                    'Tag\\V1\\Rest\\Tag' => 'Doctrine_driver',
                ],
            ],
        ],
    ],
    'router'                 => [
        'routes' => [
            'tag.rest.doctrine.tag' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/tag[/:tag_id]',
                    'defaults' => [
                        'controller' => 'Tag\\V1\\Rest\\Tag\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning'          => [
        'uri' => [
            0 => 'tag.rest.doctrine.tag',
        ],
    ],
    'zf-rest'                => [
        'Tag\\V1\\Rest\\Tag\\Controller' => [
            'listener'                   => \Tag\V1\Rest\Tag\TagResource::class,
            'route_name'                 => 'tag.rest.doctrine.tag',
            'route_identifier_name'      => 'tag_id',
            'entity_identifier_name'     => 'id',
            'collection_name'            => 'tag',
            'entity_http_methods'        => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods'    => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => null,
            'entity_class'               => \Tag\V1\Rest\Tag\Tag::class,
            'collection_class'           => \Tag\V1\Rest\Tag\TagCollection::class,
            'service_name'               => 'Tag',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers'            => [
            'Tag\\V1\\Rest\\Tag\\Controller' => 'HalJson',
        ],
        'accept-whitelist'       => [
            'Tag\\V1\\Rest\\Tag\\Controller' => [
                0 => 'application/vnd.tag.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'Tag\\V1\\Rest\\Tag\\Controller' => [
                0 => 'application/vnd.tag.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal'                 => [
        'metadata_map' => [
            \Tag\V1\Rest\Tag\Tag::class           => [
                'route_identifier_name'  => 'tag_id',
                'entity_identifier_name' => 'id',
                'route_name'             => 'tag.rest.doctrine.tag',
                'hydrator'               => 'Tag\\V1\\Rest\\Tag\\TagHydrator',
            ],
            \Tag\V1\Rest\Tag\TagCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'tag.rest.doctrine.tag',
                'is_collection'          => true,
            ],
        ],
    ],
    'zf-apigility'           => [
        'doctrine-connected' => [
            \Tag\V1\Rest\Tag\TagResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator'       => 'Tag\\V1\\Rest\\Tag\\TagHydrator',
            ],
        ],
    ],
    'doctrine-hydrator'      => [
        'Tag\\V1\\Rest\\Tag\\TagHydrator' => [
            'entity_class'           => \Tag\V1\Rest\Tag\Tag::class,
            'object_manager'         => 'doctrine.entitymanager.orm_default',
            'by_value'               => true,
            'strategies'             => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation'  => [
        'Tag\\V1\\Rest\\Tag\\Controller' => [
            'input_filter' => 'Tag\\V1\\Rest\\Tag\\Validator',
        ],
    ],
    'input_filter_specs'     => [
        'Tag\\V1\\Rest\\Tag\\Validator' => [
            0 => [
                'name'       => 'tag',
                'required'   => true,
                'filters'    => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            1 => [
                'name'       => 'context',
                'required'   => false,
                'filters'    => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 255,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
