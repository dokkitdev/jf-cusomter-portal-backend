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
        ],
        '/microservices/dokkit-extension/data/dashboard' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Dashboard Data',
                'description' => 'Returns dashboard statistics for the team',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'todays_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 0
                                ],
                                'out_of_hours_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 0
                                ],
                                'pending_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 353
                                ],
                                'progress_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 6
                                ],
                                'complete_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 23
                                ],
                                'invoiced_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 60
                                ],
                                'archived_jobs_total' => [
                                    'type' => 'integer',
                                    'example' => 6
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
        '/microservices/dokkit-extension/data/jobs' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Jobs List',
                'description' => 'Returns list of jobs with optional filters and relations',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 1
                    ],
                    [
                        'in' => 'query',
                        'name' => 'per_page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 15
                    ],
                    [
                        'in' => 'query',
                        'name' => 'order_by',
                        'required' => false,
                        'type' => 'string',
                        'example' => 'simpro_job_id'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'desc',
                        'required' => false,
                        'type' => 'boolean',
                        'example' => true
                    ],
                    [
                        'in' => 'query',
                        'name' => 'with[]',
                        'required' => false,
                        'type' => 'array',
                        'items' => [
                            'type' => 'string'
                        ],
                        'collectionFormat' => 'multi',
                        'example' => ['site', 'customer', 'recent_schedule']
                    ],
                    [
                        'in' => 'query',
                        'name' => 'with_count[]',
                        'required' => false,
                        'type' => 'array',
                        'items' => [
                            'type' => 'string'
                        ],
                        'collectionFormat' => 'multi',
                        'example' => ['job_attachments']
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 123],
                                            'simpro_job_id' => ['type' => 'string', 'example' => 'JOB-456'],
                                            'status' => ['type' => 'string', 'example' => 'pending'],
                                            'site' => ['type' => 'object'],
                                            'customer' => ['type' => 'object'],
                                            'recent_schedule' => ['type' => 'object'],
                                            'job_attachments_count' => ['type' => 'integer', 'example' => 2]
                                        ]
                                    ]
                                ],
                                'meta' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'current_page' => ['type' => 'integer', 'example' => 1],
                                        'per_page' => ['type' => 'integer', 'example' => 15],
                                        'total' => ['type' => 'integer', 'example' => 500]
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
        '/microservices/dokkit-extension/data/job/cost-centers' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Job Cost Centers',
                'description' => 'Returns list of cost centers for jobs',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'company_id',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 0
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'ID' => [
                                        'type' => 'integer',
                                        'example' => 519
                                    ],
                                    'Name' => [
                                        'type' => 'string',
                                        'example' => 'SIMPRO IT TAKEOFFS TEST'
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
        '/microservices/dokkit-extension/data/sites' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Sites',
                'description' => 'Returns list of sites with optional filters and relations',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 1
                    ],
                    [
                        'in' => 'query',
                        'name' => 'per_page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 15
                    ],
                    [
                        'in' => 'query',
                        'name' => 'order_by',
                        'required' => false,
                        'type' => 'string',
                        'example' => 'name'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'desc',
                        'required' => false,
                        'type' => 'boolean',
                        'example' => false
                    ],
                    [
                        'in' => 'query',
                        'name' => 'with[]',
                        'required' => false,
                        'type' => 'array',
                        'items' => [
                            'type' => 'string'
                        ],
                        'collectionFormat' => 'multi',
                        'example' => ['customer', 'primary_site_contact']
                    ],
                    [
                        'in' => 'query',
                        'name' => 'with_count[]',
                        'required' => false,
                        'type' => 'array',
                        'items' => [
                            'type' => 'string'
                        ],
                        'collectionFormat' => 'multi',
                        'example' => ['open_jobs']
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'current_page' => [
                                    'type' => 'integer',
                                    'example' => 1
                                ],
                                'data' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 209],
                                            'simpro_site_id' => ['type' => 'integer', 'example' => 74],
                                            'name' => ['type' => 'string', 'example' => 'Acacia Test Co'],
                                            'uprn' => ['type' => ['string','null'], 'example' => null],
                                            'postal_code' => ['type' => 'string', 'example' => '3000'],
                                            'country' => ['type' => 'string', 'example' => 'Australia']
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
        '/microservices/dokkit-extension/data/customers' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Customers',
                'description' => 'Returns list of customers with pagination',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 1
                    ],
                    [
                        'in' => 'query',
                        'name' => 'per_page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 10
                    ],
                    [
                        'in' => 'query',
                        'name' => 'order_by',
                        'required' => false,
                        'type' => 'string',
                        'example' => 'name'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'desc',
                        'required' => false,
                        'type' => 'boolean',
                        'example' => false
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'current_page' => [
                                    'type' => 'integer',
                                    'example' => 1
                                ],
                                'data' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object'
                                    ],
                                    'example' => []
                                ],
                                'first_page_url' => [
                                    'type' => 'string',
                                    'example' => 'http://localhost:8887/microservices/dokkit-extension/data/customers?page=1'
                                ],
                                'from' => [
                                    'type' => ['integer','null'],
                                    'example' => null
                                ],
                                'last_page' => [
                                    'type' => 'integer',
                                    'example' => 1
                                ],
                                'last_page_url' => [
                                    'type' => 'string',
                                    'example' => 'http://localhost:8887/microservices/dokkit-extension/data/customers?page=1'
                                ],
                                'links' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'url' => ['type' => ['string','null'], 'example' => null],
                                            'label' => ['type' => 'string', 'example' => '&laquo; Previous'],
                                            'active' => ['type' => 'boolean', 'example' => false]
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
        '/microservices/dokkit-extension/data/assets' => [
            'get' => [
                'tags' => ['Dokkit Extension'],
                'summary' => 'Get Assets',
                'description' => 'Returns paginated list of assets',
                'parameters' => [
                    [
                        'in' => 'header',
                        'name' => 'x-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXXXXXX'
                    ],
                    [
                        'in' => 'header',
                        'name' => 'x-team-api-key',
                        'required' => true,
                        'type' => 'string',
                        'example' => 'XXXXX'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 1
                    ],
                    [
                        'in' => 'query',
                        'name' => 'per_page',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 15
                    ],
                    [
                        'in' => 'query',
                        'name' => 'order_by',
                        'required' => false,
                        'type' => 'string',
                        'example' => 'simpro_asset_id'
                    ],
                    [
                        'in' => 'query',
                        'name' => 'desc',
                        'required' => false,
                        'type' => 'integer',
                        'example' => 1
                    ],
                    [
                        'in' => 'query',
                        'name' => 'with[]',
                        'required' => false,
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'example' => ['site', 'site.customer']
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Success response - paginated list of assets',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'current_page' => ['type' => 'integer', 'example' => 1],
                                'data' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 501],
                                            'simpro_asset_id' => ['type' => 'integer', 'example' => 3501],
                                            'name' => ['type' => 'string', 'example' => 'Boiler #501'],
                                            'serial_number' => ['type' => 'string', 'example' => 'SN-501-XYZ'],
                                            'site' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'integer', 'example' => 200],
                                                    'simpro_site_id' => ['type' => 'integer', 'example' => 1200],
                                                    'name' => ['type' => 'string', 'example' => 'Main Office'],
                                                    'customer' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'id' => ['type' => 'integer', 'example' => 55],
                                                            'simpro_customer_id' => ['type' => 'integer', 'example' => 705],
                                                            'name' => ['type' => 'string', 'example' => 'ACME Ltd'],
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'created_at' => ['type' => 'string', 'example' => '2025-09-23T10:15:00Z'],
                                            'updated_at' => ['type' => 'string', 'example' => '2025-09-23T11:00:00Z']
                                        ]
                                    ]
                                ],
                                'first_page_url' => ['type' => 'string', 'example' => 'http://localhost:8887/microservices/dokkit-extension/data/assets?page=1'],
                                'last_page' => ['type' => 'integer', 'example' => 10],
                                'last_page_url' => ['type' => 'string', 'example' => 'http://localhost:8887/microservices/dokkit-extension/data/assets?page=10'],
                                'links' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'url' => ['type' => 'string', 'example' => null],
                                            'label' => ['type' => 'string', 'example' => '&laquo; Previous'],
                                            'active' => ['type' => 'boolean', 'example' => false],
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
                                'message' => ['type' => 'string', 'example' => 'Unauthorized. Invalid or missing API key']
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
