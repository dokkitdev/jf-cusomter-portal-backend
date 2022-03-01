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

INSERT INTO customers(id, simpro_customer_id, name, type, created_at, updated_at) VALUES
(1, 208, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 2, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 3, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customer_user(id, customer_id, user_id) VALUES
(1, 1, 3),
(2, 2, 3);

INSERT INTO sites(id, simpro_site_id, name, postal_code, address, customer_id, uprn) VALUES
(1, 1, 'Name 1', 'UB8 1JG', 'Charter Place', 1, 'GSP-211226'),
(2, 2, 'Name 2', 'ub8 1JG', null, 2, null),
(3, 3, 'Name 3', 'UB1 1JG', null, 3, null),
(4, 4, 'Name 4', 'ub2 1JG', null, 1, null),
(5, 5, 'Name 5', null, null, null, null);

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
(2, 1, 'jobs', 'new', null);

INSERT INTO jobs(id, simpro_job_id, site_id, customer_id, stage, priority, order_no, description) VALUES
(1, 1, 1, 1, 'Progress', 'Fire Alarm - Standard 8 Hours', 'CN3268', '<div style="font-size: 10pt;">LOW PRESSURE AT BOILER&nbsp;</div><div style="font-size: 10pt;">as per email and arranged with tenant&nbsp;</div><div style="font-size: 10pt;">PM CALL</div>'),
(2, 2, 1, 1, 'Progress', 'Fire Alarm - Standard', null, null),
(3, 3, 1, 1, 'Progress', 'Intruder Alarm - Standard 4 Hours', null, null),
(4, 4, 1, 2, 'Complete', null, null, null),
(5, 5, 1, 2, 'Archived', null, null, null),
(6, 6, 2, 2, 'Archived', null, null, null),
(7, 7, 3, 3, 'Archived', null, null, null),
(8, 8, 4, 3, 'Complete', null, null, null),
(9, 9, 4, 3, 'Progress', null, null, null),
(10, 10, 4, 3, 'Progress', null, null, null);

INSERT INTO schedules(id, job_id, simpro_schedule_id, name, date, start_time, end_time) VALUES
(1, 1, 1, 'Name', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO job_catalogs(id, job_id, simpro_section_id, simpro_cost_center_id, simpro_catalog_id, simpro_original_catalog_id, name, part_no, qty) VALUES
(1, 1, 0, 0, 0, 0, 'Test', 'Test', 1);

INSERT INTO job_attachments(id, job_id, simpro_attachment_id, name) VALUES
(1, 1, 'Test', 'Test');

INSERT INTO job_work_orders(id, job_id, simpro_section_id, simpro_cost_center_id, simpro_work_order_id, name, description, date) VALUES
(1, 1, 0, 0, 0, 'Test', 'Test', '2020-10-06');