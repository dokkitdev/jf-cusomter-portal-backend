INSERT INTO notify_asset_reports (id, site_id, asset_id, created_at, updated_at) VALUES
(101, 11, 111, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO notify_asset_report_validations (id, asset_report_id, site_id, asset_id, error_type, error_text, uprn, asset_type, service_level_name, job_stage, created_at, updated_at) VALUES
(201, 101, 11, 111, 'no_uprn', 'error_text_1', null, null, null, null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(202, 101, 11, 111, 'no_fuel_type', 'error_text_2', 'uprn_value_2', 'asset_type_value_2', 'service_level_name_2', 'job_stage_value_2', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(203, 101, 11, 111, 'no_asset_make', 'error_text_3', 'uprn_value_3', 'asset_type_value_3', 'service_level_name_3', 'job_stage_value_3', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(204, 101, 12, 111, 'no_model', 'error_text_4', 'uprn_value_4', 'asset_type_value_4', 'service_level_name_4', 'job_stage_value_4', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(205, 101, 13, 111, 'service_due_tomorrow', 'error_text_5', 'uprn_value_5', 'asset_type_value_5', 'service_level_name_5', 'job_stage_value_5', '2018-10-10 10:10:10', '2018-10-10 10:10:10');