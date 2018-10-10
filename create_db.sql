DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS urls;

CREATE TABLE urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    desc TEXT NOT NULL,
    url TEXT NOT NULL,
    timeout INTEGER NOT NULL,
    status_code INTEGER NOT NULL,
    status_msg TEXT NOT NULL
);

CREATE TABLE logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url_id INTEGER NOT NULL,
    date INTEGER NOT NULL,
    status_code INTEGER NOT NULL,
    status_msg TEXT,
    name_lookup_ms INTEGER,
    connect_ms INTEGER,
    ssl_connect_ms INTEGER,
    start_transfer_ms INTEGER,
    total_ms INTEGER,
    FOREIGN KEY(url_id) REFERENCES urls(id)
);
