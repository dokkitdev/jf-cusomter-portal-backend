INSERT INTO customers(id, simpro_customer_id, name, type, created_at, updated_at) VALUES
(1, 123, 'Customer 123', 'companies', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO sites(id, simpro_site_id, name, created_at, updated_at) VALUES
(1, 234, 'Site 455', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO jobs(id, simpro_job_id, customer_id, site_id, is_repair, created_at, updated_at) VALUES
(1, 345, 1, 1, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(2, 456, 1, 1, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(3, 567, 1, 1, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO assets(id, simpro_asset_id, site_id, created_at, updated_at) VALUES
(2, 46898, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(3, 46899, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4, 46900, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO asset_test_records(id, asset_id, job_id, created_at, updated_at) VALUES
(3, 2, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4, 3, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(5, 4, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(6, 3, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(7, 2, 1, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO asset_test_record_readings(id, asset_test_record_id, name) VALUES
(4, 3, 'Test Record Reading 4'),
(5, 4, 'Test Record Reading 5'),
(6, 4, 'Test Record Reading 6'),
(7, 5, 'Test Record Reading 7'),
(8, 6, 'Test Record Reading 8'),
(9, 6, 'Test Record Reading 9'),
(10, 7, 'Test Record Reading 10');

INSERT INTO simpro_jobs(id, handle_status, handle_result, simpro_entity_id, data) VALUES
(1, 'error', null, 46899,
 '{
    "ID": "job.asset.tested",
    "name": "Job",
    "build": "gassure.simprocloud.com",
    "action": "asset.tested",
    "description": "Asset #46899 on Job # is tested",
    "date_triggered": "2023-10-20T16:20:29+01:00",
    "reference": {
      "companyID": 0
    }
 }'),
(2, 'new', null, 46899,
 '{
    "ID": "job.asset.tested",
    "name": "Job",
    "build": "gassure.simprocloud.com",
    "action": "asset.tested",
    "description": "Asset #46899 on Job # is tested",
    "date_triggered": "2023-10-20T16:20:29+01:00",
    "reference": {
      "companyID": 0
    }
 }'),
(3, 'completed', null, 46899,
 '{
    "ID": "job.asset.tested",
    "name": "Job",
    "build": "gassure.simprocloud.com",
    "action": "asset.tested",
    "description": "Asset #46899 on Job # is tested",
    "date_triggered": "2023-10-20T16:20:29+01:00",
    "reference": {
      "companyID": 0
    }
 }');