INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
  (1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customers(id, simpro_customer_id, name, type, email, created_at, updated_at) VALUES
  (1, 1, 'Simpro Customer', 'companies', 'company1@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 'Simpro Customer', 'companies', 'company2@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'Simpro Customer', 'companies', 'company3@example.com', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO customer_user(id, customer_id, user_id) VALUES
  (1, 1, 3),
  (2, 2, 3),
  (3, 3, 3);

INSERT INTO simpro_jobs(id, data, handle_status, handle_result) VALUES
  (1, '{"ID": "job.created", "build": "gassure.simprocloud.com", "description": "Job #test has been crashed.", "name": "Job", "action": "created", "reference": {"companyID": 0, "jobID": 2406}, "date_triggered": "2019-12-18T11:52:29+00:00"}', 'error', '{}');
