INSERT INTO users(id, name, email, password, role_id) VALUES
(4, 'not used', 'user4@example.com', '', 3),
(5, 'User5', 'user5@example.com', '', 3);

INSERT INTO customers (id, simpro_customer_id, name, type) VALUES
(5, 55, 'not used', 'companies'),
(6, 66, 'Customer6 User5', 'companies');

INSERT INTO sites (id, simpro_site_id, name, customer_id) VALUES
(6, 66, 'not used', null),
(7, 77, 'Site7', 6);

INSERT INTO customer_site (id, customer_id, site_id) VALUES
(7, 6, 7);

INSERT INTO customer_user (id, customer_id, user_id) VALUES
(7, 6, 5)