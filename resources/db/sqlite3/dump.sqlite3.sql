----
-- phpLiteAdmin database dump (https://bitbucket.org/phpliteadmin/public)
-- phpLiteAdmin version: 1.9.6
-- Exported: 1:32pm on March 31, 2016 (UTC)
-- database file: ./silla.db3
----
BEGIN TRANSACTION;

----
-- Table structure for cms_users
----
CREATE TABLE 'cms_users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'role_id' INTEGER NOT NULL, 'password' TEXT NOT NULL, 'email' TEXT NOT NULL, 'name' TEXT NOT NULL, 'timezone' TEXT, 'created_on' DATETIME, 'updated_on' DATETIME, 'login_on' DATETIME);

----
-- Data dump for cms_users, a total of 1 rows
----
INSERT INTO "cms_users" ("id","role_id","password","email","name","timezone","created_on","updated_on","login_on") VALUES ('1','1','$2a$12$jusORAs9Ezt5wOH7iUy4oO4iRV0EKzRgrsXng4IQoF4Psd2Cbq1zW','demo@silla.io','Demo','Europe/Sofia','2012-06-19 11:40:16','2015-03-25 23:08:19','2015-03-25 23:08:19');

----
-- Table structure for cms_userroles
----
CREATE TABLE 'cms_userroles' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'title' TEXT NOT NULL, 'created_on' DATETIME, 'updated_on' DATETIME, 'permissions' TEXT);

----
-- Data dump for cms_userroles, a total of 1 rows
----
INSERT INTO "cms_userroles" ("id","title","created_on","updated_on","permissions") VALUES ('1','Administrator','2015-02-20 03:00:00','2015-02-20 14:20:01','{"account":["credentials","edit"],"help":["show","create","edit","delete","export","index"],
   "userroles":["show","create","edit","delete","export","index"],
   "users":["show","create","edit","delete","export","index"]}');

----
-- Table structure for sessions
----
CREATE TABLE 'sessions' ('session_key' TEXT PRIMARY KEY NOT NULL, 'last_active' INTEGER NOT NULL);

----
-- Data dump for sessions, a total of 0 rows
----

----
-- Table structure for session_vars
----
CREATE TABLE 'session_vars' ('session_key' TEXT NOT NULL, 'private_key' TEXT NOT NULL, 'name' TEXT NOT NULL, 'value' TEXT, PRIMARY KEY ('session_key', 'private_key', 'name'));

----
-- Data dump for session_vars, a total of 0 rows
----

----
-- Table structure for cache
----
CREATE TABLE 'cache' ('cache_key' INTEGER PRIMARY KEY NOT NULL, 'value' TEXT NOT NULL, 'expire' NUMERIC NOT NULL);

----
-- Data dump for cache, a total of 0 rows
----

----
-- Table structure for cms_help
----
CREATE TABLE 'cms_help' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'title' TEXT NOT NULL, 'content' TEXT NOT NULL, 'created_on' DATETIME NOT NULL, 'updated_on' DATETIME NOT NULL);

----
-- Data dump for cms_help, a total of 1 rows
----
INSERT INTO "cms_help" ("id","title","content","created_on","updated_on") VALUES ('1','Overview','{"formatted":"<h1>Overview<\/h1>\n<hr \/>\n<h3>Silla.IO is a MVC based PHP Application Development Framework<\/h3>\n<p>Reusable software environment that provides particular functionality as part of a larger software platform to facilitate development of software applications, products and solutions.<\/p>\n<p>The framework comes with a CMS Application to enable building user defined content management systems.<\/p>\n<ul>\n<li>Used to run projects for global brands<\/li>\n<li>3 years in active development by a professional team<\/li>\n<li>Covers best practices and system architecture<\/li>\n<li>Complete development history available<\/li>\n<li>Complete code Documentation and available examples of CMS user documentation<\/li>\n<li>Penetration tested\n<ul>\n<li>The framework has been penetration tested by industry leading experts.<\/li>\n<li>Tested against: DoS, CSRF, Persistent and reflected XSS, Exposed download links, ClickJacking, Text injection, Order injection, Insecure HTTP methods as well as issues with password management, authentication and e-mail harvesting<\/li>\n<li>To live up to standards of multinational blue chip clients and their data security needs<\/li>\n<\/ul><\/li>\n<\/ul>\n<p><em>Learn more at <a href=\"http:\/\/silla.io\/\">Silla.IO<\/a><\/em> <\/p>","raw":"# Overview\r\n***\r\n### Silla.IO is a MVC based PHP Application Development Framework\r\n\r\nReusable software environment that provides particular functionality as part of a larger software platform to facilitate development of software applications, products and solutions.\r\n\r\nThe framework comes with a CMS Application to enable building user defined content management systems.\r\n* Used to run projects for global brands\r\n* 3 years in active development by a professional team\r\n* Covers best practices and system architecture\r\n* Complete development history available\r\n* Complete code Documentation and available examples of CMS user documentation\r\n* Penetration tested\r\n  * The framework has been penetration tested by industry leading experts.\r\n  * Tested against: DoS, CSRF, Persistent and reflected XSS, Exposed download links, ClickJacking, Text injection, Order injection, Insecure HTTP methods as well as issues with password management, authentication and e-mail harvesting\r\n  * To live up to standards of multinational blue chip clients and their data security needs\r\n\r\n*Learn more at [Silla.IO](http:\/\/silla.io\/)*"}','2016-03-01 00:00:00','2016-03-01 00:00:00');

----
-- structure for index sqlite_autoindex_sessions_1 on table sessions
----
;

----
-- structure for index sqlite_autoindex_session_vars_1 on table session_vars
----
;

----
-- structure for index  userrole on table cms_users
----
CREATE INDEX ' userrole' ON "cms_users" ("role_id");

----
-- structure for index  email on table cms_users
----
CREATE UNIQUE INDEX ' email' ON "cms_users" ("email");

----
-- structure for index  title on table cms_help
----
CREATE UNIQUE INDEX ' title' ON "cms_help" ("title");
COMMIT;
