<?php
/**
 * Created by PhpStorm.
 * User: bodyast
 * Date: 17.10.25
 * Time: 15:49
 */

return [
    'route' => '/',
    'info' => [
        'description' => 'swagger-description',
        'version' => '1.0.0',
        'title' => 'Simpro Console & Dokkit Extension API',
        'termsOfService' => '',
        'contact' => [
            'email' => 'bodyast1996@gmail.com'
        ],
        'license' => [
            'name' => '',
            'url' => ''
        ]
    ],
    'swagger' => [
        'version' => '2.0'
    ],
    'basePath' => '/',
    'schemes' => ['http', 'https'],
    'definitions' => [
        'Status' => [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'example' => 'ok'
                ],
                'service' => [
                    'type' => 'string',
                    'example' => 'Dokkit Extension'
                ],
                'timestamp' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => '2023-04-01T12:00:00+00:00'
                ]
            ]
        ],
        'Config' => [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'example' => 'Dokkit Extension'
                ],
                'version' => [
                    'type' => 'string',
                    'example' => '1.0.0'
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'example' => true
                ]
            ]
        ],
        'DataRequest' => [
            'type' => 'object',
            'required' => ['name', 'type'],
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'example' => 'Test Document'
                ],
                'description' => [
                    'type' => 'string',
                    'example' => 'This is a test document'
                ],
                'type' => [
                    'type' => 'string',
                    'enum' => ['document', 'report', 'invoice'],
                    'example' => 'document'
                ],
                'tags' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string'
                    ],
                    'example' => ['test', 'dokkit', 'example']
                ]
            ]
        ],
        'DataResponse' => [
            'type' => 'object',
            'properties' => [
                'success' => [
                    'type' => 'boolean',
                    'example' => true
                ],
                'message' => [
                    'type' => 'string',
                    'example' => 'Data processed successfully'
                ],
                'id' => [
                    'type' => 'integer',
                    'example' => 123
                ]
            ]
        ],
        'ValidationError' => [
            'type' => 'object',
            'properties' => [
                'success' => [
                    'type' => 'boolean',
                    'example' => false
                ],
                'message' => [
                    'type' => 'string',
                    'example' => 'Validation failed'
                ],
                'errors' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'string',
                                'example' => 'The name field is required'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'Error' => [
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'example' => 'Unauthorized. Invalid or missing API key'
                ]
            ]
        ]
    ],
    'paths' => [
        '/microservices/dokkit-extension/status' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Service Status',
                'description' => 'Return Status Dokkit Extension',
                'produces' => ['application/json'],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'status' => [
                                    'type' => 'string',
                                    'example' => 'ok'
                                ],
                                'service' => [
                                    'type' => 'string',
                                    'example' => 'Dokkit Extension'
                                ],
                                'timestamp' => [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                    'example' => '2023-04-01T12:00:00+00:00'
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized. Invalid or missing API key',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'example' => 'Unauthorized. Invalid or missing API key'
                                ]
                            ]
                        ]
                    ]
                ],
                'security' => [
                    [
                        'DOKKIT_EXTENSION_API_KEY' => []
                    ]
                ]
            ]
        ],
        '/microservices/dokkit-extension/add-team' => [
            'post' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Add Team',
                'description' => 'Add Team Dokkit Extension',
                'produces' => ['application/json'],
                'parameters' => [
                    [
                        'in' => 'body',
                        'name' => 'body',
                        'required' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'build_url' => [
                                    'type' => 'string',
                                    'example' => 'sandbox-uk'
                                ],
                                'token' => [
                                    'type' => 'string',
                                    'example' => '1234567891011121314151617'
                                ],
                                'is_full_update' => [
                                    'type' => 'boolean',
                                    'example' => false
                                ]
                            ],
                            'required' => ['build_url']
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'example' => 'Team added'
                                ],
                                'data' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'integer',
                                            'example' => 2
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'example' => 'sandbox-uk'
                                        ],
                                        'build_url' => [
                                            'type' => 'string',
                                            'example' => 'sandbox-uk'
                                        ],
                                        'token' => [
                                            'type' => 'string',
                                            'example' => '1234567891011121314151617'
                                        ],
                                        'api_key' => [
                                            'type' => ['string', 'null'],
                                            'example' => null
                                        ],
                                        'created_at' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-09-22T09:12:36.000000Z'
                                        ],
                                        'updated_at' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-09-23T09:32:30.000000Z'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized. Invalid or missing API key',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'example' => 'Unauthorized. Invalid or missing API key'
                                ]
                            ]
                        ]
                    ]
                ],
                'security' => [
                    [
                        'DOKKIT_EXTENSION_API_KEY' => []
                    ]
                ]
            ]
        ],
        '/microservices/dokkit-extension/update-token' => [
            'post' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Update Token',
                'description' => 'Update Token Team',
                'produces' => ['application/json'],
                'parameters' => [
                    [
                        'in' => 'body',
                        'name' => 'body',
                        'required' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'build_url' => [
                                    'type' => 'string',
                                    'example' => 'sandbox-uk'
                                ],
                                'token' => [
                                    'type' => 'string',
                                    'example' => '1234567891011121314151617'
                                ]
                            ],
                            'required' => ['build_url']
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'example' => 'Token updated'
                                ],
                                'data' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'integer',
                                            'example' => 2
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'example' => 'sandbox-uk'
                                        ],
                                        'build_url' => [
                                            'type' => 'string',
                                            'example' => 'sandbox-uk'
                                        ],
                                        'token' => [
                                            'type' => 'string',
                                            'example' => '1234567891011121314151617'
                                        ],
                                        'api_key' => [
                                            'type' => ['string', 'null'],
                                            'example' => null
                                        ],
                                        'created_at' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-09-22T09:12:36.000000Z'
                                        ],
                                        'updated_at' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'example' => '2025-09-23T09:32:30.000000Z'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Unauthorized. Invalid or missing API key',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                    'example' => 'Unauthorized. Invalid or missing API key'
                                ]
                            ]
                        ]
                    ]
                ],
                'security' => [
                    [
                        'DOKKIT_EXTENSION_API_KEY' => []
                    ]
                ]
            ]
        ]

    ],
    'securityDefinitions' => [
        'DOKKIT_EXTENSION_API_KEY' => [
            'type' => 'apiKey',
            'name' => 'DOKKIT_EXTENSION_API_KEY',
            'in' => 'header'
        ]
    ],
    'security' => [
        [
            'DOKKIT_EXTENSION_API_KEY' => []
        ]
    ],
    'defaults' => [
        'code-descriptions' => [
            '200' => 'Operation successfully done',
            '204' => 'Operation successfully done',
            '400' => 'Validation failed',
            '401' => 'Unauthorized. Invalid or missing API key',
            '404' => 'This entity not found',
            '500' => 'Server error'
        ]
    ],
    'data_collector' => \App\Support\MicroserviceDataCollector::class,
    'display_environments' => ['local', 'development', 'dev', 'testing', 'production']
];
