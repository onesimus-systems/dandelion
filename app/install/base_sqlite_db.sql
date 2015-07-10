CREATE TABLE "{{prefix}}apikey" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    keystring TEXT,
    user_id NUMERIC,
    expires NUMERIC,
    disabled NUMERIC
);;

CREATE TABLE "{{prefix}}category" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description TEXT,
    parent NUMERIC
);;

INSERT INTO "{{prefix}}category" ("id", "description", "parent") VALUES
(1, 'Logs', 0);;

CREATE TABLE "{{prefix}}cheesto" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id NUMERIC,
    fullname TEXT,
    status TEXT,
    message TEXT,
    returntime TEXT,
    modified TEXT,
    disabled NUMERIC
);;

INSERT INTO "{{prefix}}cheesto" ("id", "user_id", "fullname", "status", "message", "returntime", "modified", "disabled") VALUES
(1, 1, 'Administrator', 'Available', '', '00:00:00', '2015-05-16 21:05:24', 0);;

CREATE TABLE "{{prefix}}group" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    permissions TEXT
);;

INSERT INTO "{{prefix}}group" ("id", "name", "permissions") VALUES
(1, 'user', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:9:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:10:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:11:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:0;}'),
(2, 'admin', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:9:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:10:"createuser";b:1;s:8:"edituser";b:1;s:10:"deleteuser";b:1;s:11:"creategroup";b:1;s:9:"editgroup";b:1;s:11:"deletegroup";b:1;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:1;}'),
(3, 'guest', 'a:15:{s:9:"createlog";b:0;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:9:"createcat";b:0;s:7:"editcat";b:0;s:9:"deletecat";b:0;s:10:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:11:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:0;s:5:"admin";b:0;}');;

CREATE TABLE "{{prefix}}log" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date_created TEXT,
    time_created TEXT,
    title TEXT,
    body TEXT,
    user_id NUMERIC,
    category TEXT,
    is_edited NUMERIC,
    num_of_comments INTEGER DEFAULT 0
);;

CREATE TABLE "{{prefix}}comment" (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id NUMERIC,
  comment TEXT,
  created TEXT,
  log_id NUMERIC
);;

CREATE TABLE "{{prefix}}session" (
    id TEXT,
    data TEXT,
    last_accessed TEXT
);;

CREATE TABLE "{{prefix}}user" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT,
    password TEXT,
    fullname TEXT,
    group_id NUMERIC,
    created TEXT,
    initial_login NUMERIC,
    logs_per_page NUMERIC,
    theme TEXT,
    disabled NUMERIC
);;

INSERT INTO "{{prefix}}user" ("id", "username", "password", "fullname", "group_id", "created", "initial_login", "logs_per_page", "theme", "disabled") VALUES
(1, 'admin', '$2y$10$zibMP6jZw5PRMGHGdo/JzeXkb3re0WEIulmkgRe4PC76GwT4M8G5u', 'Administrator', 2, '2015-01-01', 1, 25, '', 0);;
