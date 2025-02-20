INSERT INTO roles(id, name, created_at, updated_at) VALUES
(1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
(1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_jobs(id, data, handle_status, handle_result) VALUES
(1, '{"ID": "job.created", "build": "pfsgroup.simprosuite.com", "description": "Job #test has been crashed.", "name": "Job", "action": "created", "reference": {"companyID": 0, "jobID": 2406}, "date_triggered": "2019-12-18T11:52:29+00:00"}', 'error', '{}');

INSERT INTO customers(id, simpro_customer_id, name, type, email, created_at, updated_at) VALUES
(1, 101, 'Simpro Customer1', 'companies', 'company1@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 102, 'Simpro Customer2', 'companies', 'company2@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 103, 'Simpro Customer3', 'companies', 'company3@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(4, 104, 'Simpro Customer4', 'individuals', 'indivi1@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(5, 105, 'Simpro Customer5', 'individuals', 'indivi2@example.com', '2018-10-20 11:05:00', '2016-10-20 11:05:00'),
(6, 106, 'Simpro Customer6', 'companies', 'company4@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customer_user(id, customer_id, user_id) VALUES
(1, 1, 3),
(2, 2, 3);

INSERT INTO sites(id, simpro_site_id, name, postal_code, address, customer_id, uprn) VALUES
(1, 1, 'Name 1', 'UB8 1JG', 'Charter Place', 1, 'uprn 1'),
(2, 2, 'Name 2', null, null, 2, null),
(3, 3, 'Name 3', null, null, 3, null),
(4, 4, 'Name 4', null, null, 3, null),
(5, 5, 'Name 5', null, null, null, null),
(6, 6, 'Name 6', null, null, null, null);

INSERT INTO site_contacts(id, site_id, simpro_contact_id, title, name, is_primary) VALUES
(1, 1, 1, 'Title', 'Name', true),
(2, 2, 2, 'Title', 'Name', true),
(3, 3, 3, 'Title', 'Name', false),
(4, 4, 4, 'Title', 'Name', true);

INSERT INTO customer_site(id, customer_id, site_id) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 1, 4),
(5, 2, 1),
(6, 3, 4);

INSERT INTO simpro_log(id, loggable_id, loggable_type, handle_status, handle_result) VALUES
(1, 1, 'sites', 'new', null),
(2, 1, 'jobs', 'new', null),
(3, 1, 'assets', 'new', null);

INSERT INTO jobs(id, simpro_job_id, site_id, customer_id, stage, logged_completion_date) VALUES
(1, 1, 1, 1, 'Progress', '2021-02-22 11:11:11'),
(2, 2, 1, 1, 'Progress', '2022-02-22 11:11:11');

INSERT INTO schedules(id, job_id, simpro_schedule_id, name, date, start_time, end_time) VALUES
(1, 2, 1, 'Name', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 2, 2, 'Name', '2018-11-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 2, 3, 'Name', '2018-11-19 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO job_catalogs(id, job_id, simpro_section_id, simpro_cost_center_id, simpro_catalog_id, simpro_original_catalog_id, name, part_no, qty) VALUES
(1, 1, 0, 0, 0, 0, 'Test', 'Test', 1);

INSERT INTO job_attachments(id, job_id, simpro_attachment_id, name) VALUES
(1, 1, 'Test', 'Test');

INSERT INTO job_work_orders(id, job_id, simpro_section_id, simpro_cost_center_id, simpro_work_order_id, name, description, date) VALUES
(1, 1, 0, 0, 0, 'Test', 'Test', '2020-10-06');

INSERT INTO assets(id, simpro_asset_id, site_id, name, sortable_date, last_test_date, next_service_date, last_test_result, service_level_name, archived, asset_type, last_cp12_date, job_id, customer_id, no_access_date_1) VALUES
(1, 1, 1, 'Name 1', '2016-10-20', '2016-10-20', '2016-10-20', 'Test result...', 'Service level...', false, 4, '2021-01-06', 1, 1, '2016-10-20'),
(2, 2, 1, 'Name 2', '2016-10-20', '2016-10-20', '2016-10-20', 'Test result...', 'Service level...', false, 4, '2021-01-07', 2, 1, '2016-10-20'),
(3, 3, 1, 'Name 3', '2021-01-10', null, null, 'Test result...', 'Service level...', true, 4, '2021-01-10', 1, 1, '2016-10-20'),
(4, 4, 1, 'Name 4', '2021-01-08', null, '2021-01-08', null, null, false, 4, '2021-01-08', 2, 1, null),
(5, 5, 1, 'Name 5', null, null, null, null, null, false, 4, null, 1, 1, null),
(6, 6, 2, 'Name 6', null, null, null, null, null, false, 4, null, 2, 1, null),
(7, 7, 3, 'Name 7', null, null, null, null, null, false, 3, null, 1, 1, null),
(8, 8, 4,  null, null, null, null, null, null, false, 3, null, 2, 1, null),
(9, 9, 4, null, null, null, null, null, null, false, 3, null, 1, 1, null),
(10, 10, 4, null, null, null, null, null, null, false, 3, null, 2, 1, null),
(11, 11, 1, 'Name 11', '2021-01-10', null, null, 'Test result...', 'Service level...', false, 4, '2021-01-10', 1, 1, '2016-10-20'),
(12, 12, 2, 'Name 12', null, null, null, null, null, true, 4, null, 2, 1, null);

INSERT INTO asset_custom_fields(id, asset_id, simpro_custom_field_id, name, value) VALUES
(1, 1, 1, 'name', 'value'),
(2, 1, 2, 'name', 'value'),
(3, 2, 3, 'name', 'value'),
(4, 3, 4, 'name', 'value');

INSERT INTO asset_attachments(id, asset_id, simpro_attachment_id, name) VALUES
(1, 1, 'link1', 'name'),
(2, 1, 'link2', 'name'),
(3, 2, 'link3', 'name'),
(4, 3, 'link4', 'name');

INSERT INTO asset_test_records(id, asset_id, job_id, name, test_date, notes, result) VALUES
(1, 1, 1, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(2, 1, 2, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(3, 2, 1, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(4, 3, 2, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(5, 4, 2, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(6, 5, 1, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(7, 6, 2, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(8, 7, 2, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(9, 8, 1, 'name', '2016-10-20', 'Some notes...', 'Pass'),
(10, 9, 2, 'name', '2016-10-20', 'Some notes...', 'Pass');

INSERT INTO asset_test_record_readings(id, asset_test_record_id, name, value) VALUES
(1, 1, 'name', 'value'),
(2, 2, 'name', 'value'),
(3, 3, 'name', 'value'),
(4, 4, 'name', 'value');

INSERT INTO asset_log_histories(id, assets_pulled_at, assets_count) VALUES
(1, '2021-05-20 07:00:00', 100),
(2, '2021-05-20 08:00:00', 100);

INSERT INTO private_contracts(id, simpro_recurring_invoice_id, next_recurring_date, direct_date, direct_month, customer_id, site_id, recurring_type, company_name, period, payer_reference, payer_account_name, payment_type, is_company, is_processed) VALUES
(1, 101, '2017-10-20 07:00:00', null, null, 1, 1, 'Service', 'Quinn, Kim', '12 months', '28769X51765 (DUF003)', 'Payne', 'Annual payment', true, false),
(2, 102, '2018-10-21 07:00:00', '15th of', 'March', 2, 2, 'Project', null, null, null, null, 'Annual payment', true, false),
(3, 103, '2018-10-20 07:00:00', null, null, 1, 1, 'Service', 'Test 3', '12 months', 'WILL15R', null, 'Direct Debit', false, true),
(4, 104, '2018-10-20 07:00:00', '1st of', 'April', 2, 2, 'Service', 'Test 4', '12 months', 'WILL15R', 'Mda', 'Annual payment', true, false);