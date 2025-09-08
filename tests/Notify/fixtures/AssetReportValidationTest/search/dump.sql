INSERT INTO notify_asset_reports (id, site_id, asset_id, uprn, asset_type, type, fuel_type, make, model, last_service_date, service_level_start_date, job_due_date, next_service_date, job_stage, service_level_name, last_mot_date, service_due, next_scheduled_appointment_date, no_access_visits, location, cancellation, created_at, updated_at) VALUES
(101, 11, 111, '', '', '', '', '', '', '2001-01-01', '2001-01-01', '2001-01-01', '2001-01-01', '', '', '', '2001-01-01', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO notify_asset_report_validations (id, asset_report_id, site_id, asset_id, uprn, asset_type, service_level_name, error, job_stage, fuel_type, created_at, updated_at) VALUES
(201, 101, 11, 111, 'uprn_value_1', 'asset_type_value_1', 'service_level_name_1', 'error_value_1', 'job_stage_value_1', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(202, 101, 11, 111, 'uprn_value_2', 'asset_type_value_2', 'service_level_name_2', 'error_value_2', 'job_stage_value_2', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(203, 101, 12, 111, 'uprn_value_3', 'asset_type_value_3', 'service_level_name_3', 'error_value_3', 'job_stage_value_3', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(204, 101, 13, 111, 'uprn_value_4', 'asset_type_value_4', 'service_level_name_4', 'error_value_4', 'job_stage_value_4', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(205, 101, 14, 111, 'uprn_value_5', 'asset_type_value_5', 'service_level_name_5', 'error_value_5', 'job_stage_value_5', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10');