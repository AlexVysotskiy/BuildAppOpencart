CREATE TABLE `oc_user_city` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1052 DEFAULT CHARSET=utf8;


INSERT INTO `oc_user_city` (`name`) VALUES ('Саратов'),('Москва'),('Санкт-петербург');

ALTER TABLE oc_user
  ADD city_id INT(11) NOT NULL
  AFTER user_group_id;

UPDATE oc_user SET city_id = 1;