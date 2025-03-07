INSERT INTO roles(id, name, created_at, updated_at) VALUES
(1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
(1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customers(id, simpro_customer_id, name, type, email, title, address, city, state, postal_code, created_at, updated_at) VALUES
(1, 101, 'Simpro Customer1', 'companies', 'company1@example.com', 'cus 1', 'address 1', 'city 1', 'state 1', 'postcode 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 102, 'Simpro Customer2', 'companies', 'company2@example.com', 'cus 2', 'address 2, street 2', 'city 2', 'state 2', 'postcode 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 103, 'Simpro Customer3', 'companies', 'company3@example.com', 'cus 3', 'address 3', 'city 3', 'state 3', 'postcode 3', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(4, 104, 'Simpro Customer4', 'individuals', 'indivi1@example.com', 'cus 4', 'address 4', 'city 4', 'state 4', 'postcode 4', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(5, 105, 'Simpro Customer5', 'individuals', 'indivi2@example.com', 'cus 5', 'address 5', 'city 5', 'state 5', 'postcode 5', '2018-10-20 11:05:00', '2016-10-20 11:05:00'),
(6, 106, 'Simpro Customer6', 'companies', 'company4@example.com', 'cus 6', 'address 6', 'city 6', 'state 6', 'postcode 6', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO sites(id, simpro_site_id, name, postal_code, address, city, country, customer_id, uprn) VALUES
(1, 1, 'Name 1', 'UB8 1JG', 'Charter Place', 'Dublin', 'Eireland', 1, 'uprn 1'),
(2, 2, 'Name 2', 'UB8 2JG', 'Street 2, Charter Place 1', 'New York', 'Venezuela', 2, null),
(3, 3, 'Name 3', null, null, null, null, 3, null),
(4, 4, 'Name 4', null, null, null, null, 3, null),
(5, 5, 'Name 5', null, null, null, null, null, null),
(6, 6, 'Name 6', null, null, null, null, null, null);

INSERT INTO private_contracts(id, simpro_recurring_invoice_id, next_recurring_date, direct_date, direct_month, customer_id, site_id, recurring_type, company_name, period, payer_reference, payer_account_name, payment_type, is_company, is_processed, docx, pdf) VALUES
(1, 101, '2017-10-20 07:00:00', null, null, 1, 1, 'Service', 'Quinn, Kim', '12 months', '28769X51765 (DUF003)', 'Payne', 'Annual payment', true, false, null, null),
(2, 102, '2018-10-21 07:00:00', '15th of', 'March', 2, 2, 'Project', null, null, null, 'Mda2', 'Annual payment', true, false, '2.Annual.2018-11-11.102.docx', '2.Annual.2018-11-11.102.pdf'),
(3, 103, '2018-10-20 07:00:00', null, null, 1, 1, 'Project', 'Test 3', '12 months', 'WILL15R', 'Mda3', 'Direct Debit', false, true, null, null),
(4, 104, '2018-10-20 07:00:00', '1st of', 'April', 2, 2, 'Service', 'Test 4', '12 months', 'WILL15R', 'Mda4', 'Annual payment', true, false, null, null),
(5, 105, '2018-10-20 07:00:00', '15th of', 'February', 2, 2, 'Service', 'Test 5', '12 months', 'WILL16R', 'Mda5', 'Direct Debit', true, false, null, null);

INSERT INTO private_contract_cost_centers(id, private_contract_id, simpro_section_id, simpro_cost_center_id, ex_tax, tax, inc_tax, section_name, name, created_at, updated_at) VALUES
(1, 1, 69, 601, 14.99, 3.00, 17.99, 'Cost Center section 1', 'Cost Center name 1', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(2, 2, 64, 602, 14.00, 3.00, 17.00, 'Cost Center section 2', 'Cost Center name 2', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(3, 2, 65, 603, 4.00, 0.20, 4.20, 'Cost Center section 3', 'Cost Center name 3', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(4, 3, 66, 604, 30.00, 10.20, 40.20, 'Cost Center section 4', 'Cost Center name 4', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(5, 4, 67, 605, 30.00, 10.20, 40.20, 'Cost Center section 5', 'Cost Center name 5', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(6, 5, 68, 605, 31.00, 11.20, 41.20, 'Cost Center section 6', 'Cost Center name 6', '2018-11-11 11:11:11', '2018-11-11 11:11:11');

INSERT INTO private_contract_cost_center_items(id, simpro_recurring_invoice_cost_center_item_id, qty, private_contract_cost_center_id, "order", ex_tax, inc_tax, type, name, created_at, updated_at) VALUES
(1, 101, 2, 1, 1, 4.99, 7.99, 'Prebuilds', 'item name 1', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(2, 102, 2, 2, 1, 3.99, 6.99, 'Prebuilds', 'item name 2', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(3, 103, 1, 2, 2, 1.99, 2.99, 'Catalogs', 'item name 3', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(4, 104, 1, 2, 3, 2.99, 3.99, 'OneOffs', 'item name 4', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(5, 105, 2, 2, 4, 3.89, 4.89, 'Discount', 'item name 5', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(6, 106, 1, 3, 1, 3.66, 6.66, 'Prebuilds', 'item name 6', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(7, 107, 1, 4, 2, 30.00, 40.20, 'OneOffs', 'item name 7', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(8, 107, 3, 5, 2, 30.00, 40.20, 'Catalogs', 'item name 8', '2018-11-11 11:11:11', '2018-11-11 11:11:11'),
(9, 108, 1, 5, 2, 31.00, 41.20, 'Catalogs', 'item name 9', '2018-11-11 11:11:11', '2018-11-11 11:11:11');
