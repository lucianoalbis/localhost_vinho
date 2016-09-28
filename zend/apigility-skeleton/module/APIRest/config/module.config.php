<?php
return [
    'router' => [
        'routes' => [
            'api-rest.rest.album' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/album[/:album_id]',
                    'defaults' => [
                        'controller' => 'APIRest\\V1\\Rest\\Album\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'api-rest.rest.album',
        ],
        'default_version' => 1,
    ],
    'zf-rest' => [
        'APIRest\\V1\\Rest\\Album\\Controller' => [
            'listener' => 'APIRest\\V1\\Rest\\Album\\AlbumResource',
            'route_name' => 'api-rest.rest.album',
            'route_identifier_name' => 'album_id',
            'collection_name' => 'album',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PUT',
                2 => 'DELETE',
                3 => 'POST',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \APIRest\V1\Rest\Album\AlbumEntity::class,
            'collection_class' => \APIRest\V1\Rest\Album\AlbumCollection::class,
            'service_name' => 'album',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'APIRest\\V1\\Rest\\Album\\Controller' => 'Json',
        ],
        'accept_whitelist' => [
            'APIRest\\V1\\Rest\\Album\\Controller' => [
                0 => 'application/vnd.api-rest.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
                3 => 'application/x-www-form-urlencoded',
            ],
        ],
        'content_type_whitelist' => [
            'APIRest\\V1\\Rest\\Album\\Controller' => [
                0 => 'application/vnd.api-rest.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \APIRest\V1\Rest\Album\AlbumEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'api-rest.rest.album',
                'route_identifier_name' => 'album_id',
                'hydrator' => \Zend\Hydrator\ArraySerializable::class,
            ],
            \APIRest\V1\Rest\Album\AlbumCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'api-rest.rest.album',
                'route_identifier_name' => 'album_id',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'db-connected' => [
            'APIRest\\V1\\Rest\\Album\\AlbumResource' => [
                'adapter_name' => 'db Mysql - skeleton-application',
                'table_name' => 'album',
                'hydrator_name' => \Zend\Hydrator\ArraySerializable::class,
                'controller_service_name' => 'APIRest\\V1\\Rest\\Album\\Controller',
                'entity_identifier_name' => 'id',
                'table_service' => 'APIRest\\V1\\Rest\\Album\\AlbumResource\\Table',
            ],
        ],
    ],
    'zf-content-validation' => [
        'APIRest\\V1\\Rest\\Album\\Controller' => [
            'input_filter' => 'APIRest\\V1\\Rest\\Album\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'APIRest\\V1\\Rest\\Album\\Validator' => [
            0 => [
                'name' => 'artist',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => '100',
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'title',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => '100',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'zf-mvc-auth' => [
        'authorization' => [
            'APIRest-V1-Rest-Album-Controller' => [
                'collection' => [
                    'GET' => true,
                    'POST' => true,
                    'PUT' => true,
                    'PATCH' => false,
                    'DELETE' => true,
                ],
                'entity' => [
                    'GET' => true,
                    'POST' => true,
                    'PUT' => true,
                    'PATCH' => false,
                    'DELETE' => true,
                ],
            ],
        ],
    ],
];
