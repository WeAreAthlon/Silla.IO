----
-- Silla.IO SQLite 3 database dump file
-- phpLiteAdmin version: 1.9.5
----
BEGIN TRANSACTION;

----
-- Table structure for cms_users
----
CREATE TABLE 'cms_users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'role_id' INTEGER NOT NULL, 'password' TEXT NOT NULL, 'email' TEXT NOT NULL, 'name' TEXT NOT NULL, 'timezone' TEXT, 'created_on' DATETIME, 'updated_on' DATETIME, 'login_on' DATETIME);

----
-- Data dump for cms_users, a total of 1 rows
----
INSERT INTO "cms_users" ("id", "role_id", "password", "email", "name", "timezone", "created_on", "updated_on", "login_on")
VALUES
  ('1', '1', '$2a$12$XOhONym.UtjUgFyYzM7L5uG2SXTre6Vx.3GIw10bRC.t/.4rMmjrq', 'demo@silla.io', 'Demo',
   'Europe/Sofia', '2012-06-19 11:40:16', '2013-04-01 23:31:52', '2013-04-01 23:31:52');

----
-- Table structure for cms_userroles
----
CREATE TABLE 'cms_userroles' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'title' TEXT NOT NULL, 'created_on' DATETIME, 'updated_on' DATETIME, 'permissions' TEXT);

----
-- Data dump for cms_userroles, a total of 1 rows
----
INSERT INTO "cms_userroles" ("id", "title", "created_on", "updated_on", "permissions") VALUES
  ('1', 'Administrator', '2015-02-20 03:00:00', '2015-02-20 14:20:01', '{"help":["show","create","edit","delete","export","index"],
   "userroles":["show","create","edit","delete","export","index"],
   "users":["account","show","create","edit","delete","export","index"]}');

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
-- Table structure for cache
----
CREATE TABLE 'cache' ('cache_key' INTEGER PRIMARY KEY NOT NULL, 'value' TEXT NOT NULL, 'expire' NUMERIC NOT NULL);

----
-- Data dump for cache, a total of 0 rows
----

----
-- structure for index sqlite_autoindex_sessions_1 on table sessions
----
;

----
-- structure for index sqlite_autoindex_session_vars_1 on table session_vars
----
;

----
-- structure for index userrole on table cms_users
----
CREATE INDEX ' userrole' ON "cms_users" ("role_id");

----
-- structure for index email on table cms_users
----
CREATE UNIQUE INDEX ' email' ON "cms_users" ("email");

----
-- structure for trigger Delete session variables on table sessions
----
CREATE TRIGGER ' DELETE session variables' AFTER DELETE ON "sessions" FOR EACH ROW BEGIN DELETE FROM session_vars
WHERE session_key = OLD.session_key;
END;
COMMIT;
