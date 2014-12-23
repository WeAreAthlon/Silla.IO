-- -----------------------------------------------------
-- Table `athlon_framework`.`cms_userroles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_userroles` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(100) NOT NULL ,
  `created_on` DATETIME NOT NULL ,
  `updated_on` DATETIME NOT NULL ,
  `permissions` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `athlon_framework`.`cms_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cms_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` SMALLINT(5) UNSIGNED NOT NULL ,
  `password` VARCHAR(60) NOT NULL ,
  `email` VARCHAR(150) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `timezone` VARCHAR(50) NOT NULL ,
  `created_on` DATETIME NOT NULL ,
  `updated_on` DATETIME NOT NULL ,
  `login_on` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email` USING BTREE (`email` ASC) ,
  INDEX `role_id` USING BTREE (`role_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `athlon_framework`.`sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sessions` (
  `session_key` CHAR(32) NOT NULL ,
  `last_active` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`session_key`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `athlon_framework`.`session_vars`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `session_vars` (
  `session_key` CHAR(32) NOT NULL ,
  `private_key` CHAR(32) NOT NULL ,
  `name` VARCHAR(20) NOT NULL ,
  `value` TEXT NULL ,
  INDEX `session_key_idx` (`session_key` ASC) ,
  UNIQUE INDEX `session_value` (`private_key` ASC, `name` ASC, `session_key` ASC) ,
  CONSTRAINT `session_key`
    FOREIGN KEY (`session_key` )
    REFERENCES `sessions` (`session_key` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Data for table `athlon_framework`.`cms_userroles`
-- -----------------------------------------------------
INSERT INTO `cms_userroles` (`id`, `title`, `created_on`, `updated_on`, `permissions`) VALUES (1, 'Administrator', '2012-06-19 00:00:00', '2013-02-12 18:02:18', '{\"help\":[\"index\"],\"userroles\":[\"create",\"edit\",\"delete\",\"index\", \"export\", \"show\"],\"users\":[\"create",\"edit\",\"delete\",\"account\",\"index\",\"export\",\"show\"]}');

-- -----------------------------------------------------
-- Data for table `athlon_framework`.`cms_users`
-- -----------------------------------------------------
INSERT INTO `cms_users` (`id`, `role_id`, `password`, `email`, `name`, `timezone`, `created_on`, `updated_on`, `login_on`) VALUES (1, 1, '$2a$12$wX61GlidotqLdWiuRYP5sOVItSdauCGlZ/V7wO1E7//4LZ92y2gqu', 'demo@silla.io', 'Demo User', 'Europe/Sofia', '2012-06-19 11:40:16', '2013-04-01 23:31:52', '2013-04-01 23:31:52');
