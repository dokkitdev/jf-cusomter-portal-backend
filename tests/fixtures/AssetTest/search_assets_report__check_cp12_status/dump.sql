INSERT INTO customers(id, simpro_customer_id, name, type, email) VALUES
(4, 444, 'Simpro Customer 4', 'companies', 'company4@example.com');

INSERT INTO customer_user(id, customer_id, user_id) VALUES
(3, 4, 3);

INSERT INTO sites(id, simpro_site_id, customer_id, name) VALUES
(6, 666, 4, 'Name 6');

INSERT INTO customer_site(id, customer_id, site_id) VALUES
(7, 4, 6);

INSERT INTO jobs(id, simpro_job_id, site_id, customer_id, stage) VALUES
(3, 333, 6, 4, 'Complete'),
(4, 444, 6, 4, 'Progress');

INSERT INTO assets(id, simpro_asset_id, site_id, job_id, archived, asset_type, next_service_date) VALUES
(13, 1313, 6, 3, false, 4, null),
(14, 1414, 6, 4, false, 4, null),
(15, 1515, 6, 4, false, 4, '2019-11-11'), --"Overdue"
(16, 1616, 6, 4, false, 4, '2019-11-12'), --"Due" bound 1
(17, 1717, 6, 4, false, 4, '2019-12-09'), --"Due" bound 2
(18, 1818, 6, 4, false, 4, '2019-12-10'); --"On Time"