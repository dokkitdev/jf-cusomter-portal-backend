INSERT INTO roles(id, name, created_at, updated_at) VALUES
(1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
(1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customers(id, simpro_customer_id, name, type, created_at, updated_at) VALUES
(1, 1, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(2, 2, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
(3, 3, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customer_user(id, customer_id, user_id) VALUES
(1, 1, 3),
(2, 2, 3),
(3, 3, 1);

INSERT INTO sites(id, simpro_site_id, name, postal_code, address) VALUES
(1, 1, 'Name 1', 'UB8 1JG', 'Charter Place'),
(2, 2, 'Name 2', null, null),
(3, 3, 'Name 3', null, null),
(4, 4, 'Name 4', null, null),
(5, 5, 'Name 5', null, null);

INSERT INTO customer_site(id, customer_id, site_id) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 1, 4),
(5, 2, 1),
(6, 3, 4);

INSERT INTO jobs(id, simpro_job_id, site_id, customer_id, stage, priority, date_created, logged_create_date) VALUES
(1, 1, 1, 1, 'Pending', 'Fire Alarm - Standard 8 Hours', '2018-11-11', '2018-11-11 06:00:00'),
(2, 2, 1, 1, 'Progress', 'Fire Alarm - Standard', '2018-11-10', '2018-11-10 18:00:00'),
(3, 3, 1, 1, 'Invoiced', 'Intruder Alarm - Standard 4 Hours', '2018-11-11', '2018-11-11 13:00:00'),
(4, 4, 1, 2, 'Complete', null, null, null),
(5, 5, 1, 2, 'Archived', null, null, null),
(6, 6, 2, 2, 'Archived', null, '2020-11-11', '2020-11-11 06:00:00'),
(7, 7, 3, 3, 'Archived', null, '2020-11-11', '2020-11-11 19:00:00'),
(8, 8, 4, 3, 'Complete', null, null, null),
(9, 9, 4, 3, 'Progress', null, null, null),
(10, 10, 4, 3, 'Invoiced', null, null, null);

