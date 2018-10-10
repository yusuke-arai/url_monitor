#!/bin/bash

DIR=$(dirname $(readlink -f $0))

if [ -f $DIR/config.json ]; then
    echo "config.json already exists."
else
    echo "Create config.json..."
    cat <<EOL > $DIR/config.json
{
    "db_file": "$DIR/db/url_monitor.sqlite3",
    "smtp_host": "localhost",
    "smtp_port": 25,
    "mail_from": "admin@example.com",
    "mail_to": [ "admin@example.com" ]
}
EOL
    echo "Please edit config.json."
    echo
fi

if [ -d $DIR/db ]; then
    echo "db directory already exists."
else
    echo "Create SQLite3 file..."
    mkdir -p -m 777 $DIR/db
    sqlite3 $DIR/db/url_monitor.sqlite3 < $DIR/create_db.sql
    chmod 666 $DIR/db/url_monitor.sqlite3
fi

if [ ! -f $DIR/simple_http_check/bin/simple_http_check ]; then
    echo "Build simple_http_check..."
    (cd simple_http_check && make)
fi
