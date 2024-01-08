CREATE DATABASE `attend` /*!40100 DEFAULT CHARACTER SET utf8 */;

use attend;

CREATE TABLE `accounts`
(
    `id`       int(11)     NOT NULL AUTO_INCREMENT,
    `username` varchar(45) NOT NULL,
    `pwhash`   varchar(60) NOT NULL,
    `email`    varchar(255) DEFAULT NULL,
    `role`     varchar(45)  DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 41
  DEFAULT CHARSET = utf8;


CREATE TABLE `token_auths`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `cookie_hash` varchar(255) NOT NULL,
    `expires`     timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `account_id`  int(11)      NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE `login_attempts`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `attempted_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `username`     varchar(45)           DEFAULT NULL,
    `pass`         binary(1)    NOT NULL DEFAULT '1',
    `note`         varchar(255) NOT NULL DEFAULT 'OK',
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 39
  DEFAULT CHARSET = utf8;

CREATE TABLE `classrooms`
(
    `id`         int(11)     NOT NULL AUTO_INCREMENT,
    `label`      varchar(45) NOT NULL,
    `ordering`   int(11)              DEFAULT NULL,
    `created_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp   NULL     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    UNIQUE KEY `name_UNIQUE` (`label`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 29
  DEFAULT CHARSET = utf8;



CREATE TABLE `students`
(
    `id`           int(10) unsigned NOT NULL AUTO_INCREMENT,
    `family_name`  varchar(45)      NOT NULL,
    `first_name`   varchar(45)      NOT NULL,
    `enrolled`     int(1)           NOT NULL DEFAULT '0',
    `classroom_id` int(10)                   DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    KEY `fk_student_classroom_idx` (`classroom_id`),
    CONSTRAINT `fk_student_classroom` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB
  AUTO_INCREMENT = 691
  DEFAULT CHARSET = utf8;

CREATE TABLE `schedules`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `student_id` int(10) unsigned NOT NULL,
    `schedule`   int(11)          NOT NULL DEFAULT '0',
    `start_date` date             NOT NULL,
    `entered_at` int(11)          NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    UNIQUE KEY `student_date_unique` (`student_id`, `start_date`),
    KEY `fk_student_idx` (`student_id`),
    CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB
  AUTO_INCREMENT = 782
  DEFAULT CHARSET = utf8 COMMENT ='Table indicating when students are scheduled to attend';


CREATE TABLE `attendance`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `student_id` int(10) unsigned NOT NULL,
    `check_in`   int(11) DEFAULT NULL,
    `check_out`  int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    KEY `fk_students_idx` (`student_id`),
    CONSTRAINT `fk_students` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO classrooms (label)
VALUES ("123's"),
       ("ABC's"),
       ("Pre-K");



CREATE DEFINER = `attend`@`localhost` TRIGGER `attend`.`classrooms_BEFORE_INSERT`
    BEFORE INSERT
    ON `classrooms`
    FOR EACH ROW
BEGIN
    DECLARE mytemp INT(11);
    IF NEW.ordering = '' then
        SELECT MAX(ordering) FROM classrooms INTO mytemp;
        SET NEW.ordering = mytemp + 1;
    END IF;
END
CREATE DEFINER = `attend`@`localhost` TRIGGER `attend`.`classrooms_BEFORE_UPDATE` BEFORE UPDATE ON `classrooms` FOR EACH ROW
BEGIN
SET NEW.updated_at = CURRENT_TIMESTAMP;
END


CREATE USER `attend`@`localhost` identified by 'attend';

grant all on attend.* to `attend`@`localhost`;
