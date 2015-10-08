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

-- Dumping database structure for tablet_data
CREATE DATABASE IF NOT EXISTS `tablet_data` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `tablet_data`;


-- Dumping structure for table tablet_data.foreign_site_child_analysis
CREATE TABLE IF NOT EXISTS `foreign_site_child_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `assesment_phase` varchar(25) NOT NULL,
  `age` varchar(20) DEFAULT '0',
  `gender` varchar(6) NOT NULL DEFAULT 'NA',
  `receptive_vocabulary` smallint(6) DEFAULT '0',
  `letter_name_identification_in_alphabetical_order` smallint(6) DEFAULT '0',
  `letter_name_identification_in_random_order` smallint(6) DEFAULT '0',
  `sound_letter_identification` smallint(6) DEFAULT '0',
  `decodeable_words_and_sight_words` smallint(6) DEFAULT '0',
  `phonological_awareness_rhyming` smallint(6) DEFAULT '0',
  `phonological_awareness_blending` smallint(6) DEFAULT '0',
  `phonological_awareness_non_word_repetition` smallint(6) DEFAULT '0',
  `total_score` smallint(6) DEFAULT '0',
  `percentage` decimal(10,7) DEFAULT '0.0000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
