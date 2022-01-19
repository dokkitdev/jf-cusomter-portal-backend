<?php

namespace App\Tests\Support;

use App\Models\SimproJob;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\HttpRequestService;

trait SimproTestTrait
{
    protected function createSimproJob($fixture)
    {
        $webhookData = $this->getJsonFixture($fixture);
        $webhookData['data'] = json_decode($webhookData['data'], true);
        SimproJob::create($webhookData);
    }

    protected function mockApproveQuote()
    {
        $this->mockHttpRequestService([
            $this->patchQuoteRequest(),
            $this->postQuoteNoteRequest()
        ]);
    }

    protected function mockPostQuoteNote()
    {
        $this->mockHttpRequestService([
            $this->postQuoteNoteRequest()
        ]);
    }

    protected function mockDeclineQuote()
    {
        $this->mockHttpRequestService([
            $this->postQuoteNoteRequest(),
            $this->patchQuoteRequest()
        ]);
    }

    protected function mockReRequestQuote()
    {
        $this->mockHttpRequestService([
            $this->postQuoteNoteRequest(),
            $this->patchQuoteRequest()
        ]);
    }

    protected function postQuoteNoteRequest()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820/notes/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_quote_note_response_success.json'
            ]
        ];
    }

    protected function patchQuoteRequest()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_quote_response_success.json'
            ]
        ];
    }

    protected function mockCreateOrUpdateQuote()
    {
        $this->mockHttpRequestService([
            $this->getQuote(),
            $this->getCustomer(),
            $this->getJob(),
            $this->getCustomer(),
            $this->getSite(),
            $this->getSiteContacts(),
            $this->getJobAttachments('get_job_attachments_response_success.json'),
            $this->getJobAttachments('get_job_attachments_empty_response_success.json'),
            $this->getQuoteNote(),
            $this->getQuoteNoteAttachments()
        ]);
    }

    protected function mockCreateOrUpdateAsset()
    {
        $this->mockHttpRequestService([
            $this->getAsset(),
            $this->getSite(),
            $this->getCustomer(),
            $this->getSiteContacts(),
            $this->getAssetServiceLevels(),
            $this->getAssetAttachments('get_asset_attachments_response_success.json'),
            $this->getAssetAttachments('get_asset_attachments_empty_response_success.json'),
            $this->getAssetTestHistories(),
            $this->getJob(),
            $this->getJobAttachments('get_job_attachments_response_success.json'),
            $this->getJobAttachments('get_job_attachments_empty_response_success.json'),
            $this->getJob(),
            $this->getJobAttachments('get_job_attachments_response_success.json'),
            $this->getJobAttachments('get_job_attachments_empty_response_success.json'),
        ]);
    }

    protected function mockDownloadAssetAttachment()
    {
        $this->mockHttpRequestService([
            $this->getAssetAttachmentFile(),
        ]);
    }

    protected function mockGetAssetServiceLevels()
    {
        $this->mockHttpRequestService([
            $this->getAssetsServiceLevels('get_assets_service_levels_response_success.json'),
            $this->getAssetsServiceLevels('get_assets_service_levels_empty_response_success.json'),
        ]);
    }

    protected function getAsset()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customerAssets/10000'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_asset_response_success.json'
            ]
        ];
    }

    protected function getAssetServiceLevels()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2752/assets/10000/serviceLevels/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_asset_service_levels_response_success.json'
            ]
        ];
    }

    protected function mockCreateOrUpdateInvoice()
    {
        $this->mockHttpRequestService([
            $this->getCustomerInvoice(),
        ]);
    }

    protected function mockUpdateQuoteCommand()
    {
        $this->mockHttpRequestService([
            $this->getQuoteNote(),
            $this->getQuoteNoteAttachments(),
            $this->getQuoteNoteAttachments(),
        ]);
    }

    protected function mockUpdateGroupsCommand()
    {
        $this->mockHttpRequestService([
            $this->getSites('get_sites_response_success.json'),
            $this->getSites('get_sites_empty_response_success.json'),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSites('get_sites_response_success.json'),
            $this->getSites('get_sites_empty_response_success.json'),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
        ]);
    }

    protected function mockUpdateSitesCommand()
    {
        $this->mockHttpRequestService([
            $this->getSite(),
            $this->getSite(),
            $this->getCustomer(),
            $this->getSiteContacts(),
            $this->getSite(),
            $this->getSite(),
            $this->getSiteContacts(),
        ]);
    }

    protected function mockCreateQuoteRequest()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_quote_response_success.json'
                ]
            ],
            $this->mockPostQuoteAttachment(),
            $this->mockPostQuoteAttachment()
        ]);
    }

    protected function mockCreatejobRequest()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_job_response_success.json'
                ]
            ],
            $this->mockPostJobAttachment(),
            $this->mockPostJobAttachment()
        ]);
    }

    protected function getQuote()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_response_success.json'
            ]
        ];
    }

    protected function getQuoteNote()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_response_success.json'
            ]
        ];
    }

    protected function getQuoteNoteAttachments()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_attachments_response_success.json'
            ]
        ];
    }

    protected function getAssetAttachments($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2765/assets/10000/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getAssetTestHistories()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2765/assets/10000/testHistory/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_asset_test_histories_response_success.json'
            ]
        ];
    }

    protected function mockPostJobAttachment()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/210600/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_job_attachment_response_success.json'
            ]
        ];
    }

    protected function mockPostQuoteAttachment()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_quote_attachment_response_success.json'
            ]
        ];
    }

    protected function mockPatchSiteContact()
    {
        $this->mockHttpRequestService([
            $this->patchSiteContact()
        ]);
    }

    protected function mockDeleteSiteContact()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'delete',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/11026'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'delete_site_contact_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockPostSiteContact()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_site_contact_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockUpdateSite()
    {
        $this->mockHttpRequestService([
            $this->patchSite(),
            $this->patchSiteContact(),
            $this->patchSiteCustomField(),
            $this->patchSiteCustomField()
        ]);
    }

    protected function mockUpdateSiteByAdmin()
    {
        $this->mockHttpRequestService([
            $this->patchSite(),
            $this->patchSiteContact(),
            $this->patchSiteCustomField()
        ]);
    }

    protected function patchSite()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_response_success.json'
            ]
        ];
    }

    protected function patchSiteContact()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/11026'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_contact_response_success.json'
            ]
        ];
    }

    protected function patchSiteCustomField()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/customFields/22'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_custom_field_response_success.json'
            ]
        ];
    }

    protected function mockCreateOrUpdateSite()
    {
        $this->mockHttpRequestService([
            $this->getSite(),
            $this->getSiteContacts()
        ]);
    }

    protected function mockGetJobs()
    {
        $this->mockHttpRequestService([
            $this->getJobs('get_jobs_response_success.json'),
            $this->getJobs('get_jobs_empty_response_success.json'),
        ]);
    }

    protected function getJobs($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockUpdateJobsCommand()
    {
        $this->mockHttpRequestService([
            $this->getJob(),
            $this->getJob(),
        ]);
    }

    protected function mockDownloadJobAttachment()
    {
        $this->mockHttpRequestService([
            $this->getJobAttachmentFile(),
        ]);
    }

    protected function mockDownloadQuoteNoteAttachment()
    {
        $this->mockHttpRequestService([
            $this->getQuoteNoteAttachmentFile(),
        ]);
    }

    protected function mockCreateOrUpdateSchedule()
    {
        $this->mockHttpRequestService([
            $this->getSchedule()
        ]);
    }

    protected function mockCreateOrUpdateJob()
    {
        $this->mockHttpRequestService([
            $this->getJob(),
            $this->getCustomer(),
            $this->getSite(),
            $this->getSiteContacts(),
            $this->getSchedules('get_schedules_response_success.json'),
            $this->getSchedules('get_schedules_empty_response_success.json'),
            $this->getJobAttachments('get_job_attachments_response_success.json'),
            $this->getJobAttachments('get_job_attachments_empty_response_success.json'),
            $this->getJobWorkOrders(),
            $this->getJobInvoices('get_invoices_response_success.json'),
            $this->getCustomerInvoice(),
            $this->getJobInvoices('get_invoices_empty_response_success.json'),
        ]);
    }

    protected function mockGetCustomer()
    {
        $this->mockHttpRequestService([
            $this->getCustomer()
        ]);
    }

    protected function getJobAttachmentFile()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/2406/attachments/files/7Dcva_XBYo8fqg1hOtixXE5jSFDabVFFdU6l5GdS2FI/view/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_attachment_file_response_success.json'
            ]
        ];
    }

    protected function getQuoteNoteAttachmentFile()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256/attachments/files/1n9nS2sI3NaTnu0kSDa_XpqOGMMm_VuQ_awG0Mn4E0g/view/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_attachment_file_response_success.json'
            ]
        ];
    }

    protected function getAssetAttachmentFile()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2765/assets/10000/attachments/files/1n9nS2sI3NaTnu0kSDa_XpqOGMMm_VuQ_awG0Mn4E0g'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_asset_attachment_file_response_success.json'
            ]
        ];
    }

    protected function getAssetsServiceLevels($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/assets/serviceLevels/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getJobInvoices($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/invoices/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getCustomerInvoice()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customerInvoices/103987'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_customer_invoice_response_success.json'
            ]
        ];
    }

    protected function getJobWorkOrders()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/sections/9049/costCenters/9092/workOrders/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_work_orders_response_success.json'
            ]
        ];
    }

    protected function getJobAttachments($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getSchedules($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/schedules/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getSchedule()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/schedules/18734'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_schedule_response_success.json'
            ]
        ];
    }

    protected function getJob()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_response_success.json'
            ]
        ];
    }

    protected function getSite()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2406'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_site_response_success.json'
            ]
        ];
    }

    protected function getSiteContacts()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2406/contacts'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_site_contacts_response_success.json'
            ]
        ];
    }

    protected function getCustomer()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customers/companies/6'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_customer_response_success.json'
            ]
        ];
    }

    protected function mockGetGroupSites()
    {
        $this->mockHttpRequestService([
            $this->getSites('get_sites_response_success.json'),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSites('get_sites_empty_response_success.json'),
        ]);
    }

    protected function mockGetSites()
    {
        $this->mockHttpRequestService([
            $this->getSites('get_sites_response_success.json'),
            $this->getSites('get_sites_empty_response_success.json'),
        ]);
    }

    protected function mockGetQuotes()
    {
        $this->mockHttpRequestService([
            $this->getQuotes('get_quotes_response_success.json'),
            $this->getQuotes('get_quotes_empty_response_success.json'),
        ]);
    }

    protected function getQuotes($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetInvoices()
    {
        $this->mockHttpRequestService([
            $this->getInvoices('get_invoices_response_success.json'),
            $this->getInvoices('get_invoices_empty_response_success.json'),
        ]);
    }

    protected function mockGetAssets()
    {
        $this->mockHttpRequestService([
            $this->getAssets('get_assets_response_success.json'),
            $this->getAssets('get_assets_empty_response_success.json'),
        ]);
    }

    protected function getInvoices($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customerInvoices/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getAssets($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customerAssets/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function getSites($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockSyncCustomersCommand()
    {
        $this->mockHttpRequestService([
            $this->getCustomers('companies', 'sync_company_customers_response_success.json'),
            $this->getCustomers('companies', 'sync_company_customers_empty_response_success.json'),
            $this->getCustomers('individuals', 'sync_individual_customers_response_success.json'),
            $this->getCustomers('individuals', 'sync_individual_customers_empty_response_success.json'),
        ]);
    }

    protected function getCustomers($type, $fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo("https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customers/{$type}"),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetProjectTags()
    {
        $this->mockHttpRequestService([
            $this->getProjectTags('get_project_tags_response_success.json'),
            $this->getProjectTags('get_project_tags_empty_response_success.json'),
        ]);
    }

    protected function getProjectTags($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/tags/projects'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetProjectCustomFields()
    {
        $this->mockHttpRequestService([
            $this->getProjectCustomFields('get_project_custom_fields_response_success.json'),
            $this->getProjectCustomFields('get_project_custom_fields_empty_response_success.json'),
        ]);
    }

    protected function getProjectCustomFields($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup .simprosuite.com/api/v1.0/companies/0/setup/customFields/projects'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetResponseTimes()
    {
        $this->mockHttpRequestService([
            $this->getResponseTimes('get_response_times_response_success.json'),
            $this->getResponseTimes('get_response_times_empty_response_success.json'),
        ]);
    }

    protected function getResponseTimes($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/responseTimes'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetCostCenters()
    {
        $this->mockHttpRequestService([
            $this->getCostCenters('get_cost_centers_response_success.json'),
            $this->getCostCenters('get_cost_centers_empty_response_success.json'),
        ]);
    }

    protected function getCostCenters($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/accounts/costCenters/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockGetBusinessGroups()
    {
        $this->mockHttpRequestService([
            $this->getBusinessGroups('get_business_groups_response_success.json'),
            $this->getBusinessGroups('get_business_groups_empty_response_success.json'),
        ]);
    }

    protected function getBusinessGroups($fixture)
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/accounts/businessGroups/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => $fixture
            ]
        ];
    }

    protected function mockHttpRequestService(array $requestsChain = [], string $className = HttpRequestService::class): void
    {
        $mock = $this->getMockBuilder($className)->onlyMethods(['sendRequest'])->getMock();

        foreach ($requestsChain as $index => $request) {
            $response = $this->makeResponse(Arr::get($request, 'response.fixture'));

            $mock
                ->expects($this->at($index))
                ->method('sendRequest')
                ->willReturn($response);
        }

        $this->app->instance($className, $mock);
    }

    protected function makeResponse($fixture = null): GuzzleResponse
    {
        $response = is_array($fixture) ? json_encode($fixture) : $this->getJsonFixture($fixture, true);

        $status = $response['status'];
        $headers = $response['headers'];
        $response = $response['content'];

        return new GuzzleResponse($status, $headers, $response);
    }
}