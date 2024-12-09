INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'customer', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at, set_password_hash_created_at) VALUES
  (1, 'User 1', 'user1@example.com', '', null, null, 2, '2018-10-10 10:10:10', '2018-10-10 10:10:10', null),
  (2, 'User 2', 'user2@example.com', '', null, null, 2, '2018-10-10 10:10:10', '2018-10-10 10:10:10', null),
  (3, 'User 3', 'user3@example.com', '', null, null, 2, '2018-10-10 10:10:10', '2018-10-10 10:10:10', null);

INSERT INTO authentication_codes(id, user_id, code, expires_at) VALUES
  (1, 3, '333333', '2018-10-10 10:10:10'),
  (2, 1, '111111', '2018-10-10 10:10:10'),
  (3, 2, '222222', '2018-10-10 10:10:10');



