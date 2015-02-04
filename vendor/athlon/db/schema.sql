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
-- Data for table `cms_userroles`
-- -----------------------------------------------------

INSERT INTO `cms_userroles` (`id`, `title`, `created_on`, `updated_on`, `permissions`) VALUES (1, 'Administrator', '2012-06-19 00:00:00', '2013-02-12 18:02:18', '{\"help\":[\"index\"],\"userroles\":[\"add\",\"edit\",\"delete\",\"index\"],\"users\":[\"add\",\"edit\",\"delete\",\"account\",\"index\"]}');

-- -----------------------------------------------------
-- Data for table `cms_users`
-- -----------------------------------------------------

INSERT INTO `cms_users` (`id`, `role_id`, `password`, `email`, `name`, `timezone`, `created_on`, `updated_on`, `login_on`) VALUES (1, 1, '$2a$12$wX61GlidotqLdWiuRYP5sOVItSdauCGlZ/V7wO1E7//4LZ92y2gqu', 'demo@silla.io', 'Demo', 'Europe/Sofia', '2012-06-19 11:40:16', '2013-04-01 23:31:52', '2013-04-01 23:31:52');
