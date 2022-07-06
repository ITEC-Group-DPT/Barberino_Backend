/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP DATABASE IF EXISTS `u448613383_theshear2`;
CREATE DATABASE IF NOT EXISTS `u448613383_theshear2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `u448613383_theshear2`;

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time_expected` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `canceled` tinyint(1) NOT NULL DEFAULT 0,
  `cancellation_reason` text DEFAULT NULL,
  PRIMARY KEY (`appointment_id`),
  KEY `FK_client_appointment` (`client_id`),
  KEY `FK_employee_appointment` (`employee_id`),
  CONSTRAINT `FK_client_appointment` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_employee_appointment` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

INSERT INTO `appointments` (`appointment_id`, `date_created`, `client_id`, `employee_id`, `start_time`, `end_time_expected`, `canceled`, `cancellation_reason`) VALUES
	(52, '2022-05-05 22:24:00', 1, 4, '2022-05-06 09:30:00', '2022-05-06 10:30:00', 0, NULL),
	(53, '2022-05-05 22:24:00', 1, 4, '2022-05-08 11:00:00', '2022-05-08 12:00:00', 0, NULL),
	(54, '2022-05-05 22:25:00', 1, 4, '2022-05-06 12:45:00', '2022-05-06 13:20:00', 0, NULL),
	(55, '2022-05-05 22:29:00', 1, 4, '2022-05-06 10:45:00', '2022-05-06 11:35:00', 0, NULL),
	(56, '2022-06-20 01:35:00', 1, 4, '2022-06-20 09:30:00', '2022-06-20 09:55:00', 0, NULL),
	(57, '2022-06-20 22:54:00', 7, 4, '2022-06-21 10:00:00', '2022-06-21 11:00:00', 0, NULL),
	(58, '2022-06-20 22:57:00', 7, 4, '2022-06-21 12:00:00', '2022-06-21 13:00:00', 0, NULL);

DROP TABLE IF EXISTS `barber_admin`;
CREATE TABLE IF NOT EXISTS `barber_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `barber_admin` (`admin_id`, `username`, `email`, `full_name`, `password`) VALUES
	(1, 'admin', 'jairiidriss@gmail.com', 'Idriss Jairi', 'd033e22ae348aeb5660fc2140aec35850c4da997');

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `client_email` varchar(50) NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_email` (`client_email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `clients` (`client_id`, `first_name`, `last_name`, `phone_number`, `client_email`) VALUES
	(1, 'Phú', '', '0945461850', 'tezukashuko@gmail.com'),
	(2, 'Trương', 'Minh Nam Phú', '1148484844', 'tezukashukoasdasd@gmail.com'),
	(3, 'asdas', ' ', '1231231312', 'asd09@asd.com'),
	(4, 'Phú', '', '84912361850', 'tezukashuksdfsdfsdfsdfo@gmail.com'),
	(5, 'asda', 'asdas', '0147258369', 'tezukashukoasfyut@gmail.com'),
	(6, 'asdasdasdzczx', '', '0123123123', 'zxc@gmail.com'),
	(7, 'qweqweqw', '', '84945461850', 'tezukashukoaqweqwe@gmail.com');

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `employees` (`employee_id`, `first_name`, `last_name`, `phone_number`, `email`) VALUES
	(4, 'Ruby', 'Last ', '0987654321', 'tezukashuko@gmail.com'),
	(5, 'Ruby2', 'Last', '0987654321', 'tezukashuko@gmail.com');

DROP TABLE IF EXISTS `employees_schedule`;
CREATE TABLE IF NOT EXISTS `employees_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `day_id` tinyint(1) NOT NULL,
  `from_hour` time NOT NULL,
  `to_hour` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_emp` (`employee_id`),
  CONSTRAINT `FK_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

INSERT INTO `employees_schedule` (`id`, `employee_id`, `day_id`, `from_hour`, `to_hour`) VALUES
	(83, 4, 1, '09:30:00', '18:00:00'),
	(84, 4, 2, '09:30:00', '18:00:00'),
	(85, 4, 3, '09:30:00', '18:00:00'),
	(86, 4, 4, '09:30:00', '18:00:00'),
	(87, 4, 5, '09:30:00', '18:00:00'),
	(88, 4, 6, '09:30:00', '18:00:00'),
	(89, 4, 7, '11:00:00', '17:00:00'),
	(90, 5, 1, '09:30:00', '18:00:00'),
	(91, 5, 2, '09:30:00', '18:00:00'),
	(92, 5, 3, '09:30:00', '18:00:00'),
	(93, 5, 4, '09:30:00', '18:00:00'),
	(94, 5, 5, '09:30:00', '18:00:00'),
	(95, 5, 6, '09:30:00', '18:00:00'),
	(96, 5, 7, '09:30:00', '18:00:00');

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(50) NOT NULL,
  `service_description` varchar(255) NOT NULL,
  `service_price` decimal(6,2) NOT NULL,
  `service_duration` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`service_id`),
  KEY `FK_service_category` (`category_id`),
  CONSTRAINT `FK_service_category` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `services` (`service_id`, `service_name`, `service_description`, `service_price`, `service_duration`, `category_id`) VALUES
	(1, 'Hair Cut', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 21.00, 20, 4),
	(2, 'Hair Styling', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 9.00, 15, 4),
	(3, 'Hair Triming', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 10.00, 10, 4),
	(4, 'Clean Shaving', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 20.00, 20, 2),
	(5, 'Beard Triming', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 20.00, 15, 2),
	(6, 'Smooth Shave', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 15.00, 20, 2),
	(7, 'White Facial', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 16.00, 15, 3),
	(8, 'Face Cleaning', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 20.00, 20, 3),
	(9, 'Bright Tuning', 'Barber is a person whose occupation is mainly to cut dress groom style and shave men', 14.00, 20, 3);

DROP TABLE IF EXISTS `services_booked`;
CREATE TABLE IF NOT EXISTS `services_booked` (
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  PRIMARY KEY (`appointment_id`,`service_id`),
  KEY `FK_SB_service` (`service_id`),
  CONSTRAINT `FK_SB_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SB_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `services_booked` (`appointment_id`, `service_id`) VALUES
	(52, 3),
	(52, 5),
	(52, 6),
	(52, 7),
	(53, 3),
	(53, 5),
	(53, 6),
	(53, 7),
	(54, 4),
	(54, 5),
	(55, 2),
	(55, 5),
	(55, 6),
	(56, 3),
	(56, 5),
	(57, 1),
	(57, 4),
	(57, 6),
	(58, 1),
	(58, 4),
	(58, 6);

DROP TABLE IF EXISTS `service_categories`;
CREATE TABLE IF NOT EXISTS `service_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `service_categories` (`category_id`, `category_name`) VALUES
	(2, 'Shaving'),
	(3, 'Face Masking'),
	(4, 'Uncategorized');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
