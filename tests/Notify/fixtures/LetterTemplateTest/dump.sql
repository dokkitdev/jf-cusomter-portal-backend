INSERT INTO roles(id, name, created_at, updated_at) VALUES
(1, 'administrator', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(2, 'user', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(3, 'customer', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO users(id, name, email, role_id, password, created_at, updated_at) VALUES
(1, 'Admin', 'admin@example.com', 1, '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(2, 'Not Admin', 'not_admin@example.com', 2, '', '2018-10-10 10:10:10', '2018-10-10 10:10:10');