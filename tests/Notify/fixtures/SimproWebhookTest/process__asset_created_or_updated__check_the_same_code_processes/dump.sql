INSERT INTO notify_simpro_webhooks(id, status, created_at, updated_at, data) VALUES
(1, 'pending', '2018-10-10 10:10:10', '2018-10-10 10:10:10', '{
    "ID": "asset.created",
    "reference": {
        "siteID": 11,
        "assetID": 111
    }
}'),
(2, 'pending', '2018-10-10 10:10:10', '2018-10-10 10:10:10', '{
    "ID": "asset.updated",
    "reference": {
        "siteID": 11,
        "assetID": 111
    }
}');