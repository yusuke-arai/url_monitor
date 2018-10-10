#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pprint import pprint
from datetime import datetime
import base64
import json
import os
import smtplib
from email.mime.text import MIMEText
from email.header import Header
import sqlite3
import subprocess

def send_mail(mail_from, mail_to, desc, url, date, msg, failure, smtp_host='localhost', smtp_port=25):
    if (failure):
        mail_subject = u'[URL Error] %s' %(desc)
        status = 'ERROR'
    else:
        mail_subject = u'[URL Recover] %s' %(desc)
        status = 'OK'

    mail_body = u'''Name  : %s
URL   : %s
Date  : %s
Status: %s
Error : %s

https://pandora.youworks.com/url_check/
''' %(desc, url, date, status, msg)

    mail_data = MIMEText(mail_body, 'plain', 'utf-8')
    mail_data['From'] = mail_from
    mail_data['To'] = ','.join(mail_to)
    mail_data['Subject'] = Header(mail_subject, 'utf-8')


    s = smtplib.SMTP('localhost')
    s.sendmail(mail_from, mail_to, mail_data.as_string())
    s.quit()


def main():
    dir_path = os.path.dirname(os.path.realpath(__file__))
    with open(dir_path + '/config.json') as f:
        config = json.load(f)

    db_file = config['db_file']

    db = sqlite3.connect(db_file)
    urls = db.execute("SELECT id, desc, url, timeout, status_code FROM urls;").fetchall()
    db.close()

    for row in urls:
        (id, desc, url, timeout, prev_status) = row
        res_json = subprocess.check_output([dir_path + '/simple_http_check/bin/simple_http_check', url])
        res = json.loads(res_json)
        date = datetime.now().strftime("%y/%m/%d %h:%m:%s")

        db = sqlite3.connect(db_file, isolation_level='EXCLUSIVE')
        if 'curl_error_code' in res:
            db.execute("INSERT INTO logs (url_id, date, status_code, status_msg) VALUES (:url_id, :date, :status_code, :status_msg);",
                {"url_id": id, "date": date, "status_code": res['curl_error_code'], "status_msg": res['curl_error_msg']})
            db.execute("UPDATE urls SET status_code = :status_code, status_msg = :status_msg WHERE id = :id;",
                {"id": id, "status_code": res['curl_error_code'], "status_msg": res['curl_error_msg']})

            if (prev_status <= 0):
                send_mail(config['mail_from'], config['mail_to'], desc, url, date, res['curl_error_msg'], True, smtp_host=config['smtp_host'], smtp_port=config['smtp_port'])
        else:
            db.execute("INSERT INTO logs (url_id, date, status_code, status_msg, name_lookup_ms, connect_ms, ssl_connect_ms, start_transfer_ms, total_ms) " +
                    "VALUES (:url_id, :date, :status_code, :status_msg, :name_lookup_ms, :connect_ms, :ssl_connect_ms, :start_transfer_ms, :total_ms);",
                {"url_id": id, "date": date, "status_code": 0, "status_msg": '', "name_lookup_ms": res['name_lookup'], "connect_ms": res['connect'], "ssl_connect_ms": res['ssl_connect'], "start_transfer_ms": res['start_transfer'], "total_ms": res['total']})
            db.execute("UPDATE urls SET status_code = :status_code, status_msg = :status_msg WHERE id = :id;",
                {"id": id, "status_code": 0, "status_msg": 'Ok'})

            if (prev_status > 0):
                send_mail(config['mail_from'], config['mail_to'], desc, url, date, 'Ok', False, smtp_host=config['smtp_host'], smtp_port=config['smtp_port'])

        db.commit()
        db.close()

if __name__ == "__main__":
    main()