Hi, this is Rapyd Demo Module

Server Requirements For this demo:

- php >= 5.1
- pdo_sqlite driver enabled *
- folder&files /modules/demo/db/ must have rwx permission (777)

This demo use a flat file sqlite3 database, but rapyd has also drivers for mysql and postgres.


* on a debian/ubuntu you can install sqlite driver with:
sudo apt-get install php5-sqlite

* on a windows environment check php.ini and uncomment:
extension=php_pdo_sqlite.dll


