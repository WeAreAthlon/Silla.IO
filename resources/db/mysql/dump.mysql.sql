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
  (1, 'Administrator', '2015-02-20 03:00:00', '2015-02-20 14:20:01', '{"help":["index"],"userroles":["show","create","edit","delete","export","index"],"users":["account","show","create","edit","delete","export","index"]}');

-- -----------------------------------------------------
-- Data for table `cms_users`
-- -----------------------------------------------------

INSERT INTO `cms_users` (`id`, `role_id`, `password`, `email`, `name`, `timezone`, `created_on`, `updated_on`, `login_on`) VALUES (1, 1, '$2a$12$wX61GlidotqLdWiuRYP5sOVItSdauCGlZ/V7wO1E7//4LZ92y2gqu', 'demo@silla.io', 'Demo', 'Europe/Sofia', '2012-06-19 11:40:16', '2013-04-01 23:31:52', '2013-04-01 23:31:52');

-- -----------------------------------------------------
-- Data for table `cms_settings`
-- -----------------------------------------------------

INSERT INTO `cms_settings` (`id`, `name`, `title`, `content`, `created_on`, `updated_on`) VALUES (1,'help','Help','#_User manual_\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam blandit elementum augue, ut finibus mauris fringilla at. Sed non nunc tempus, feugiat sapien vel, mollis sem. Nullam pulvinar blandit lorem, a faucibus dui fermentum vel. Nunc ut volutpat lacus, vitae iaculis mi. Aenean in semper metus, non commodo sem. Morbi nisi diam, vehicula a ultricies id, varius sed metus. Quisque hendrerit nisi ac rhoncus ultrices. Curabitur iaculis felis lorem, a ullamcorper orci auctor nec. Nam eget iaculis leo, sed vestibulum diam. Sed tincidunt ultricies metus in lacinia. Aliquam ut elit congue, consequat mauris id, congue felis. Sed sit amet sapien metus. Aenean vitae risus at turpis aliquam sollicitudin non eu nibh. Pellentesque accumsan mauris vitae laoreet egestas. ','2015-07-01 16:00:00','2015-07-03 09:45:37');
