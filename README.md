Position-checker
================
This application is under license GNU GPL version 3
Author: Jan Vaclavik
First release date: 17.5.2012

Universal web application for scanning positions in search engines. 


Docs in Czech
--------------
Position checker was my bachelor thesis in Czech Technical University, Faculty of Information Technology:
https://dip.felk.cvut.cz/browse/pdfcache/vaclaja7_2012bach.pdf

###Abstract
This bachelor thesis deals with the issues of search engines optimization and
the methods of evaluating success rate of the optimization. The focus is put
on automated evaluation of results by means of registering positions in search
engines. The automatization is based on daily detection of positions in search
engines for speciﬁc websites and keywords using searching web pages and
processing search results. The acquired data are saved into a storage and
may be processed into graphs or tables, which lay the foundations of SEO
specialist’s job.


Installing application
----------------------
1) install LAMP/WAMP/MAMP… (apache, php, mysql)

2) install Symfony 2 framework (http://symfony.com)

3) starting apache and mysql by rc scripts:
apache2 start
mysql start

4) copy position-checker to your htdocs dir (/srv/www/htdocs).

5) create database and structure (with basic data) 
mysql -uroot < db/basic-structure.sql
You can use phpMyAdmin as alternative way.

5) configure web application in:
/srv/www/htdocs/position-checker/app/config/parameters.ini

Check data in your database and mailer configuration:
database_host = 127.0.0.1
database_port = 3306
database_name = position_checker
database_user = mysql
database_password = ...
mailer_transport = smtp
mailer_host = localhost
mailer_user = jmeno@example.com
mailer_password = ...

6) run application
http://localhost/position-checker/web/


URL for CRON
-------------
http://localhost/position-checker/web/cron/scan - automatically check for all positions
http://localhost/position-checker/web/cron/newsletter - automatically send newsletter


