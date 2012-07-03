SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `genre`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genre` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `name` VARCHAR(200) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `parent_id` (`parent_id` ASC) ,
  CONSTRAINT `genre_ibfk_1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `genre` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `reviewer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `reviewer` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(1000) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 101
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `song`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `song` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `name` VARCHAR(1000) NOT NULL DEFAULT '' ,
  `artist` VARCHAR(1000) NOT NULL DEFAULT '' ,
  `album` VARCHAR(1000) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `review`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `review` (
  `reviewer_id` INT(10) UNSIGNED NOT NULL ,
  `song_id` INT(10) UNSIGNED NOT NULL ,
  `review` TEXT NOT NULL ,
  PRIMARY KEY (`reviewer_id`, `song_id`) ,
  INDEX `fk_reviewer_has_song_song1` (`song_id` ASC) ,
  INDEX `fk_reviewer_has_song_reviewer1` (`reviewer_id` ASC) ,
  CONSTRAINT `fk_reviewer_has_song_reviewer1`
    FOREIGN KEY (`reviewer_id` )
    REFERENCES `reviewer` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_reviewer_has_song_song1`
    FOREIGN KEY (`song_id` )
    REFERENCES `song` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `song_genre`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `song_genre` (
  `song_id` INT(10) UNSIGNED NOT NULL ,
  `genre_id` INT(10) UNSIGNED NOT NULL ,
  `is_primary` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`song_id`, `genre_id`) ,
  INDEX `genre_id` (`genre_id` ASC, `song_id` ASC) ,
  CONSTRAINT `song_genre_ibfk_1`
    FOREIGN KEY (`song_id` )
    REFERENCES `song` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `song_genre_ibfk_2`
    FOREIGN KEY (`genre_id` )
    REFERENCES `genre` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
