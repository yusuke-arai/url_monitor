# URL monitor

## Requirements

* [GCC](https://www.gnu.org/software/gcc/)
* make
* [libcurl](https://curl.haxx.se/libcurl/)
* PHP >= 5.4 (with SQLite3 module)
* Python >= 2.7

## Setup

```
git clone --recurse-submodules https://github.com/yusuke-arai/url_monitor.git
cd url_monitor
```

Run `setup.sh`.

```
./setup.sh
```

Publish via web server.
Set the document root to `webroot` directory.

To monitor periodically, add a schedule to cron.
For example, as follows.
```
*/5 * * * * python2 /path/to/url_monitor/http_check.py
0 0 * * * python2 /path/to/url_monitor/delete_old_logs.py
```
