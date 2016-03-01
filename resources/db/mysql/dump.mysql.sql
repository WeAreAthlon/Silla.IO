-- -----------------------------------------------------
-- Table `cms_userroles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_userroles` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `updated_on` DATETIME NOT NULL,
  `permissions` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `cms_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` SMALLINT(5) UNSIGNED NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `timezone` VARCHAR(50) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `updated_on` DATETIME NOT NULL,
  `login_on` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` USING BTREE (`email` ASC),
  INDEX `role_id` USING BTREE (`role_id` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `session_key` CHAR(32) NOT NULL,
  `last_active` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`session_key`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `session_vars`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `session_vars` (
  `session_key` CHAR(32) NOT NULL,
  `private_key` CHAR(32) NOT NULL,
  `name` VARCHAR(20) NOT NULL,
  `value` TEXT NULL,
  INDEX `session_key_idx` (`session_key` ASC),
  UNIQUE INDEX `session_value` (`private_key` ASC, `name` ASC, `session_key` ASC),
  CONSTRAINT `session_key`
    FOREIGN KEY (`session_key`)
    REFERENCES `sessions` (`session_key`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cache`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `cache_key` CHAR(32) NOT NULL,
  `value` TEXT NOT NULL,
  `expire` INT(11) NOT NULL,
  PRIMARY KEY (`cache_key`))
ENGINE = MyISAM;

-- -----------------------------------------------------
-- Table `cms_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`))
ENGINE=MyISAM;

-- -----------------------------------------------------
-- Data for table `cms_userroles`
-- -----------------------------------------------------

INSERT INTO `cms_userroles` (`id`, `title`, `created_on`, `updated_on`, `permissions`) VALUES
  (1, 'Administrator', '2015-02-20 03:00:00', '2015-02-20 14:20:01', '{"help":["show","create","edit","delete","export","index"],"userroles":["show","create","edit","delete","export","index"],"users":["account","show","create","edit","delete","export","index"]}');

-- -----------------------------------------------------
-- Data for table `cms_users`
-- -----------------------------------------------------

INSERT INTO `cms_users` (`id`, `role_id`, `password`, `email`, `name`, `timezone`, `created_on`, `updated_on`, `login_on`) VALUES (1, 1, '$2a$12$wX61GlidotqLdWiuRYP5sOVItSdauCGlZ/V7wO1E7//4LZ92y2gqu', 'demo@silla.io', 'Demo', 'Europe/Sofia', '2012-06-19 11:40:16', '2013-04-01 23:31:52', '2013-04-01 23:31:52');

-- -----------------------------------------------------
-- Data for table `cms_settings`
-- -----------------------------------------------------

INSERT INTO `cms_settings` (`id`, `name`, `title`, `content`, `created_on`, `updated_on`) VALUES (1,'help','Help','#_User manual_\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam blandit elementum augue, ut finibus mauris fringilla at. Sed non nunc tempus, feugiat sapien vel, mollis sem. Nullam pulvinar blandit lorem, a faucibus dui fermentum vel. Nunc ut volutpat lacus, vitae iaculis mi. Aenean in semper metus, non commodo sem. Morbi nisi diam, vehicula a ultricies id, varius sed metus. Quisque hendrerit nisi ac rhoncus ultrices. Curabitur iaculis felis lorem, a ullamcorper orci auctor nec. Nam eget iaculis leo, sed vestibulum diam. Sed tincidunt ultricies metus in lacinia. Aliquam ut elit congue, consequat mauris id, congue felis. Sed sit amet sapien metus. Aenean vitae risus at turpis aliquam sollicitudin non eu nibh. Pellentesque accumsan mauris vitae laoreet egestas. ','2015-07-01 16:00:00','2015-07-03 09:45:37');

-- -----------------------------------------------------
-- Data for table `cms_help`
-- -----------------------------------------------------
START TRANSACTION;
USE `athlon_framework`;
INSERT INTO `athlon_framework`.`cms_help` (`id`, `title`, `content`, `created_on`, `updated_on`) VALUES (1, 'Overview', 'a:2:{s:9:\"formatted\";s:1235:\"<h1>Overview</h1>\n<hr />\n<h3>Silla.IO is a MVC based PHP Application Development Framework</h3>\n<p>Reusable software environment that provides particular functionality as part of a larger software platform to facilitate development of software applications, products and solutions.</p>\n<p>The framework comes with a CMS Application to enable building user defined content management systems.</p>\n<ul>\n<li>Used to run projects for global brands</li>\n<li>3 years in active development by a professional team</li>\n<li>Covers best practices and system architecture</li>\n<li>Complete development history available</li>\n<li>Complete code Documentation and available examples of CMS user documentation</li>\n<li>Penetration tested\n<ul>\n<li>The framework has been penetration tested by industry leading experts.</li>\n<li>Tested against: DoS, CSRF, Persistent and reflected XSS, Exposed download links, ClickJacking, Text injection,   Order injection, Insecure HTTP methods as well as issues with password management, authentication and e-mail harvesting</li>\n<li>To live up to standards of multinational blue chip clients and their data security needs</li>\n</ul></li>\n</ul>\n<p><em>Learn more at <a href=\"http://silla.io/\">silla.io</a></em> </p>\";s:3:\"raw\";s:1122:\"# Overview\n***\n### Silla.IO is a MVC based PHP Application Development Framework\n\nReusable software environment that provides particular functionality as part of a larger software platform to facilitate development of software applications, products and solutions.\n\nThe framework comes with a CMS Application to enable building user defined content management systems.\n* Used to run projects for global brands\n* 3 years in active development by a professional team\n* Covers best practices and system architecture\n* Complete development history available\n* Complete code Documentation and available examples of CMS user documentation\n* Penetration tested\n  * The framework has been penetration tested by industry leading experts.\n  * Tested against: DoS, CSRF, Persistent and reflected XSS, Exposed download links, ClickJacking, Text injection,   Order injection, Insecure HTTP methods as well as issues with password management, authentication and e-mail harvesting\n  * To live up to standards of multinational blue chip clients and their data security needs\n\n*Learn more at [silla.io](http://silla.io/)* \";}', '2016-03-01 00:00:00', '2016-03-01 00:00:00');

COMMIT;
