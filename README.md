
# This project is developed in  CodeIgniter 4, For API Client  GuzzleHttp 7 is used

# Server Requirements CodeIgniter 4 
PHP version 7.2 or newer is required, with the *intl* extension and *mbstring* extension installed.

The following PHP extensions should be enabled on your server: php-json, php-mysqlnd, php-xml

In order to use the CURLRequest, you will need libcurl installed.

A database is required for most web application programming. Currently supported databases are:

MySQL (5.1+) via the MySQLi driver


# Installation Process

Clone the git repository

to your server and  import the database file  which is in data folder in root directory.

keep the database name as covid_tracker and then import.

No need to change this==========

'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'covid_tracker',
# ==========================

Finally start the server using command

php spark serve

Then in browser view this app

http://localhost:8080/








