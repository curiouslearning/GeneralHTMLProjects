-- --------------------------------------------------------
-- Host:                         glpprimaryinstance.criol67pfjrn.us-west-2.rds.amazonaws.com
-- Server version:               5.6.21-log - MySQL Community Server (GPL)
-- Server OS:                    Linux
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for phase_one_data
CREATE DATABASE IF NOT EXISTS `phase_one_data` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `phase_one_data`;


-- Dumping structure for table phase_one_data.assessment_app_data
CREATE TABLE IF NOT EXISTS `assessment_app_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `fun_file_id` int(11) NOT NULL COMMENT 'id in funf_file table',
  `device_id` int(11) NOT NULL COMMENT 'id in device table',
  `visual_stimuli` text COMMENT 'Filename of visual assets',
  `audio_stumuli` text COMMENT 'Filename of audio assets',
  `category` text NOT NULL COMMENT 'One group from the question categories',
  `target_stimuli` text NOT NULL COMMENT 'Filename of the asset',
  `question_time_to_first_touch` decimal(10,0) NOT NULL COMMENT 'Time from the question appearing on the screen to the first touch',
  `first_touch_to_answer_time` decimal(10,0) NOT NULL COMMENT 'Time from the first touch on the screen until the answer is selected',
  `answer_type` text NOT NULL COMMENT 'Either: Correct, Incorrect, or Timeout',
  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time when the row was created',
  `modified_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date and time when the row was updated',
  PRIMARY KEY (`id`),
  KEY `fk_device_assessment_app_data_idx` (`device_id`),
  KEY `fk_funf_file_assessment_app_data_idx` (`fun_file_id`),
  CONSTRAINT `fk_device_assessment_app_data` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`),
  CONSTRAINT `fk_funf_file_assessment_app_data` FOREIGN KEY (`fun_file_id`) REFERENCES `funf_file` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
