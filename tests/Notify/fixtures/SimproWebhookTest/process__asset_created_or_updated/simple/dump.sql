INSERT INTO notify_asset_reports (id, site_id, asset_id, uprn, asset_type, type, fuel_type, make, model, last_service_date, service_level_start_date, job_due_date, next_service_date, job_stage, service_level_name, last_mot_date, service_due, next_scheduled_appointment_date, no_access_visits, location, cancellation, created_at, updated_at) VALUES
(101, 12, 111, '', '', '', '', '', '', '2001-01-01', '2001-01-01', '2001-01-01', '2001-01-01', '', '', '', '2001-01-01', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(102, 11, 111, '', '', '', '', '', '', '2001-01-01', '2001-01-01', '2001-01-01', '2001-01-01', '', '', '', '2001-01-01', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(103, 11, 112, '', '', '', '', '', '', '2001-01-01', '2001-01-01', '2001-01-01', '2001-01-01', '', '', '', '2001-01-01', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO notify_asset_report_validations (id, asset_report_id, site_id, asset_id, uprn, fuel_type, asset_type, service_level_name, job_stage, error, created_at, updated_at) VALUES
(201, 101, 12, 111, '', '', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(202, 102, 11, 111, '', '', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(203, 102, 11, 111, '', '', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(204, 103, 11, 112, '', '', '', '', '', '', '2018-10-10 10:10:10', '2018-10-10 10:10:10');