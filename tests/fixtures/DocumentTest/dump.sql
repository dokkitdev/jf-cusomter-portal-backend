INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
  (1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'Customer', 'customer@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO media(id, name, owner_id, is_public, link, created_at, updated_at, deleted_at) VALUES
  (1, 'doc1', 1 , true, 'link1', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (2, 'doc2', 1, false, 'link2', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (3, 'doc3', 1 , true, 'link3', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (4, 'doc4', 1, false, 'link4', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (5, 'doc5', 1, false, 'link5', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);

INSERT INTO documents(id, media_id, title, description, created_at, updated_at) VALUES
  (1, 1, 'Docname 1', 'Product main photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 'Docname 2', 'Category Photo photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'Docname 3', 'Product main photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 'Docname 4', 'Category Photo photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00');