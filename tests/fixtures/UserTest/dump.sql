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