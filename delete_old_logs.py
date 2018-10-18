#!/usr/bin/env python
# -*- coding: utf-8 -*-

from datetime import datetime, timedelta
import json
import os
import sqlite3

DAYS_TO_LIVE = 30

def main():
    dir_path = os.path.dirname(os.path.realpath(__file__))
    with open(dir_path + '/config.json') as f:
        config = json.load(f)

    db_file = config['db_file']

    db = sqlite3.connect(db_file)
    limit = datetime.now() - timedelta(days=DAYS_TO_LIVE)
    db.execute("DELETE FROM logs WHERE date < :date;", {"date": limit.strftime("%Y/%m/%d %H:%M:%S")})
    db.close()


if __name__ == "__main__":
    main()
