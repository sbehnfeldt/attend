
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- accounts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(31) NOT NULL,
    `pwhash` VARCHAR(63) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `role` VARCHAR(31),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `username` (`username`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- attendance
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `student_id` INTEGER NOT NULL,
    `check_in` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `check_out` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `student_id` (`student_id`),
    CONSTRAINT `attendance_ibfk_1`
        FOREIGN KEY (`student_id`)
        REFERENCES `students` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- classrooms
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `classrooms`;

CREATE TABLE `classrooms`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(45) NOT NULL,
    `ordering` INTEGER,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    UNIQUE INDEX `name_UNIQUE` (`label`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- group_members
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `group_members`;

CREATE TABLE `group_members`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `group_id` INTEGER NOT NULL,
    `account_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `group_id` (`group_id`),
    INDEX `account_id` (`account_id`),
    CONSTRAINT `group_members_ibfk_1`
        FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `group_members_ibfk_2`
        FOREIGN KEY (`account_id`)
        REFERENCES `accounts` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- group_permissions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `group_permissions`;

CREATE TABLE `group_permissions`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `group_id` INTEGER NOT NULL,
    `permission_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `group_id` (`group_id`),
    INDEX `permission_id` (`permission_id`),
    CONSTRAINT `group_permissions_ibfk_1`
        FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `group_permissions_ibfk_2`
        FOREIGN KEY (`permission_id`)
        REFERENCES `permissions` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- groups
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(127) NOT NULL COMMENT 'Human-readable name of the group',
    `description` VARCHAR(1024) DEFAULT '' NOT NULL COMMENT 'Description of the role or purpose of the group',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name` (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- individual_permissions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `individual_permissions`;

CREATE TABLE `individual_permissions`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `account_id` INTEGER NOT NULL,
    `permissions_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `account_id` (`account_id`),
    INDEX `permissions_id` (`permissions_id`),
    CONSTRAINT `individual_permissions_ibfk_1`
        FOREIGN KEY (`account_id`)
        REFERENCES `accounts` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `individual_permissions_ibfk_2`
        FOREIGN KEY (`permissions_id`)
        REFERENCES `permissions` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- login_attempts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `attempted_at` bigint unsigned NOT NULL,
    `username` VARCHAR(63) NOT NULL,
    `pass` TINYINT(1) NOT NULL,
    `note` VARCHAR(255) NOT NULL,
    `logged_out_at` bigint unsigned,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- permissions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(127) NOT NULL COMMENT 'Human-readable mnemonic for the permission name',
    `description` VARCHAR(1024) DEFAULT '' NOT NULL COMMENT 'Description of what the permission permits',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `slug` (`slug`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- schedules
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `schedules`;

CREATE TABLE `schedules`
(
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `student_id` INTEGER NOT NULL,
    `schedule` INTEGER DEFAULT 0 NOT NULL,
    `start_date` DATE NOT NULL,
    `entered_at` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    UNIQUE INDEX `student_date_unique` (`student_id`, `start_date`),
    INDEX `fk_student_idx` (`student_id`),
    CONSTRAINT `schedules_students_FK`
        FOREIGN KEY (`student_id`)
        REFERENCES `students` (`id`)
) ENGINE=InnoDB COMMENT='Table indicating when students are scheduled to attend';

-- ---------------------------------------------------------------------
-- students
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `students`;

CREATE TABLE `students`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `family_name` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `enrolled` TINYINT(1) NOT NULL,
    `classroom_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `classroom_id` (`classroom_id`),
    CONSTRAINT `students_ibfk_1`
        FOREIGN KEY (`classroom_id`)
        REFERENCES `classrooms` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- token_auths
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `token_auths`;

CREATE TABLE `token_auths`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `cookie_hash` VARCHAR(255) NOT NULL,
    `expires` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `account_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `account_id` (`account_id`),
    CONSTRAINT `token_auths_ibfk_1`
        FOREIGN KEY (`account_id`)
        REFERENCES `accounts` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
