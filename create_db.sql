DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS urls;

CREATE TABLE urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    desc TEXT NOT NULL,
    url TEXT NOT NULL,
    timeout INTEGER NOT NULL,
    retry INTEGER NOT NULL,
    alert_on_errors_continue BOOLEAN NOT NULL,
    status_code INTEGER NOT NULL,
    errors_count INTEGER NOT NULL,
    message TEXT NOT NULL,
    modified INTEGER
);

CREATE TABLE logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url_id INTEGER NOT NULL,
    date INTEGER NOT NULL,
    curl_status_code INTEGER NOT NULL,
    response_code INTEGER,
    message TEXT,
    name_lookup_ms INTEGER,
    connect_ms INTEGER,
    ssl_connect_ms INTEGER,
    start_transfer_ms INTEGER,
    total_ms INTEGER,
    FOREIGN KEY(url_id) REFERENCES urls(id)
);
