INSERT INTO users(id, name, email, password, role_id) VALUES
(4, 'not used', 'user4@example.com', '', 3),
(5, 'Has Permission Site 7 and 9', 'user5@example.com', '', 3),
(6, 'No Permission Site 7', 'user6@example.com', '', 3),
(7, 'Not Customer', 'user7@example.com', '', 2);

INSERT INTO customers (id, simpro_customer_id, name, type) VALUES
(5, 55, 'not used', 'companies'),
(6, 66, 'Customer6 User5', 'companies'),
(7, 66, 'Customer7 User6', 'companies');

INSERT INTO sites (id, simpro_site_id, name, customer_id) VALUES
(6, 66, 'not used', null),
(7, 77, 'Site7', 5),
(8, 88, 'Site8', 7),
(9, 99, 'Site9', null);

INSERT INTO customer_site (id, customer_id, site_id) VALUES
(7,  5, 6),
(8,  6, 7),
(9,  7, 8),
(10, 6, 9);

INSERT INTO customer_user (id, customer_id, user_id) VALUES
(7, 6, 5),
(8, 7, 6);