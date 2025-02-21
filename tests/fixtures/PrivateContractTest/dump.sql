INSERT INTO roles(id, name, created_at, updated_at) VALUES
(1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
(1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customers(id, simpro_customer_id, name, type, email, created_at, updated_at) VALUES
(1, 101, 'Simpro Customer1', 'companies', 'company1@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 102, 'Simpro Customer2', 'companies', 'company2@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 103, 'Simpro Customer3', 'companies', 'company3@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(4, 104, 'Simpro Customer4', 'individuals', 'indivi1@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(5, 105, 'Simpro Customer5', 'individuals', 'indivi2@example.com', '2018-10-20 11:05:00', '2016-10-20 11:05:00'),
(6, 106, 'Simpro Customer6', 'companies', 'company4@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO sites(id, simpro_site_id, name, postal_code, address, customer_id, uprn) VALUES
(1, 1, 'Name 1', 'UB8 1JG', 'Charter Place', 1, 'uprn 1'),
(2, 2, 'Name 2', null, null, 2, null),
(3, 3, 'Name 3', null, null, 3, null),
(4, 4, 'Name 4', null, null, 3, null),
(5, 5, 'Name 5', null, null, null, null),
(6, 6, 'Name 6', null, null, null, null);

INSERT INTO private_contracts(id, simpro_recurring_invoice_id, next_recurring_date, direct_date, direct_month, customer_id, site_id, recurring_type, company_name, period, payer_reference, payer_account_name, payment_type, is_company, is_processed) VALUES
(1, 101, '2017-10-20 07:00:00', null, null, 1, 1, 'Service', 'Quinn, Kim', '12 months', '28769X51765 (DUF003)', 'Payne', 'Annual payment', true, false),
(2, 102, '2018-10-21 07:00:00', '15th of', 'March', 2, 2, 'Project', null, null, null, null, 'Annual payment', true, false),
(3, 103, '2018-10-20 07:00:00', null, null, 1, 1, 'Service', 'Test 3', '12 months', 'WILL15R', null, 'Direct Debit', false, true),
(4, 104, '2018-10-20 07:00:00', '1st of', 'April', 2, 2, 'Service', 'Test 4', '12 months', 'WILL15R', 'Mda', 'Annual payment', true, false);