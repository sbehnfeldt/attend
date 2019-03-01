
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
