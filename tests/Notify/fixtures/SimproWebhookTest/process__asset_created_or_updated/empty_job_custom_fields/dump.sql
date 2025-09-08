INSERT INTO notify_asset_reports (id, site_id, asset_id, created_at, updated_at) VALUES
(101, 12, 111, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(102, 11, 111, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(103, 11, 112, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO notify_asset_report_validations (id, asset_report_id, site_id, asset_id, error_type, error_text, created_at, updated_at) VALUES
(201, 101, 12, 111, 'no_uprn', '','2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(202, 102, 11, 111, 'no_uprn', '','2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(203, 102, 11, 111, 'no_uprn', '','2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(204, 103, 11, 112, 'no_uprn', '','2018-10-10 10:10:10', '2018-10-10 10:10:10');