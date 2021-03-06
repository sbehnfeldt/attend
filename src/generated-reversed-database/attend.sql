
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
    `username` VARCHAR(45) NOT NULL,
    `pwhash` VARCHAR(60) NOT NULL,
    `email` VARCHAR(255),
    `role` VARCHAR(45),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    UNIQUE INDEX `username_UNIQUE` (`username`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- attendance
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `student_id` int(10) unsigned NOT NULL,
    `check_in` INTEGER,
    `check_out` INTEGER,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    INDEX `fk_students_idx` (`student_id`),
    CONSTRAINT `fk_students`
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
-- login_attempts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `username` VARCHAR(45),
    `pass` binary(1) DEFAULT '1' NOT NULL,
    `note` VARCHAR(45) DEFAULT 'OK' NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- schedules
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `schedules`;

CREATE TABLE `schedules`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `student_id` int(10) unsigned NOT NULL,
    `schedule` INTEGER DEFAULT 0 NOT NULL,
    `start_date` DATE NOT NULL,
    `entered_at` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    UNIQUE INDEX `student_date_unique` (`student_id`, `start_date`),
    INDEX `fk_student_idx` (`student_id`),
    CONSTRAINT `fk_student`
        FOREIGN KEY (`student_id`)
        REFERENCES `students` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- students
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `students`;

CREATE TABLE `students`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `family_name` VARCHAR(45) NOT NULL,
    `first_name` VARCHAR(45) NOT NULL,
    `enrolled` INTEGER(1) DEFAULT 0 NOT NULL,
    `classroom_id` INTEGER(10),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id`),
    INDEX `fk_student_classroom_idx` (`classroom_id`),
    CONSTRAINT `fk_student_classroom`
        FOREIGN KEY (`classroom_id`)
        REFERENCES `classrooms` (`id`)
        ON DELETE SET NULL
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
    UNIQUE INDEX `id_UNIQUE` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
