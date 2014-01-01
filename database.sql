SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `album` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `artist` bigint(20) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `coverimage` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artist` (`artist`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `artist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `profileimage` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cms_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the name of the category',
  `parent` int(10) NOT NULL,
  `page_link` bigint(100) NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'published, unpublished, etc',
  PRIMARY KEY (`id`),
  KEY `page_link` (`page_link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_menu` (
  `id_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'category',
  `id` int(10) NOT NULL,
  `parent_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `parent_id` int(10) DEFAULT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_notifications` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unique PHP generated ID (to prevent too big INTs)',
  `title` int(11) NOT NULL COMMENT 'the title of the notification',
  `content` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'the text of the notification',
  `date` datetime NOT NULL COMMENT 'the date when the notification occured',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'whether the notification is marked as hidden or not',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` bigint(100) NOT NULL AUTO_INCREMENT,
  `site` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `useview` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'use a view to display this page or use the content from the record?',
  `templatefile` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(2) NOT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_pages` (`id`, `site`, `url`, `title`, `content`, `useview`, `templatefile`, `status`, `protected`, `hidden`) VALUES
(2, 'default', 'login', 'login', '', 1, 'login', 1, 0, 0),
(3, 'default', 'post', 'posts', '', 1, 'post', 1, 0, 0),
(4, 'default', 'home', 'homepage', '', 1, 'home', 1, 0, 0),
(5, 'default', 'logout', 'logout', '', 1, 'logout', 1, 0, 0),
(10, 'api', 'home', 'API', '', 1, 'home', 1, 1, 1),
(12, 'api', 'terminal', 'Terminal', '', 1, 'terminal', 1, 1, 1),
(13, 'cms', '404', 'Page not Found', '', 1, '404', 1, 1, 1),
(14, 'cms', 'maintenance', 'Maintenance', '', 1, 'maintenance', 1, 1, 1),
(15, 'cms', '500', 'Internal Server Error', '', 1, '500', 1, 1, 1),
(16, 'cms', '403', 'Access Denied', '', 1, '403', 1, 1, 1),
(17, 'dashboard', 'home', 'Dashboard', '', 1, 'home', 1, 1, 0),
(18, 'default', 'register', 'Register', '', 1, 'register', 1, 0, 0),
(19, 'api', 'home', 'API', '', 1, 'home', 1, 1, 1),
(20, 'api', 'terminal', 'terminal', '', 1, 'terminal', 1, 0, 1),
(21, 'construction', 'home', 'Homepage', '', 1, 'home', 1, 0, 0),
(22, 'liquidsoap', 'updateindex', 'updateindex', '', 1, 'updateindex', 1, 0, 0),
(23, 'liquidsoap', 'getnexttrack', 'getnexttrack', '', 1, 'getnexttrack', 1, 0, 0),
(24, 'api', 'emberstore', 'emberstore', '', 1, 'emberstore', 1, 1, 0),
(26, 'api', 'upload', 'upload', '', 1, 'upload', 1, 1, 1);

CREATE TABLE IF NOT EXISTS `cms_perms` (
  `id` bigint(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `default_value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_perms` (`id`, `name`, `default_value`) VALUES
(3, 'maintenance', 0),
(4, 'adminpanel', 0),
(5, 'requesttrack', 0),
(6, 'controlshoutzor', 0),
(7, 'createaccount', 0),
(8, 'upload', 0);

CREATE TABLE IF NOT EXISTS `cms_perm_roles` (
  `id` bigint(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `protected` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `hidden` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_perm_roles` (`id`, `name`, `protected`, `hidden`) VALUES
(1, 'user', '1', '0'),
(2, 'moderator', '1', '0'),
(3, 'admin', '1', '0'),
(4, 'custom', '1', '1');

CREATE TABLE IF NOT EXISTS `cms_perm_roles_parent` (
  `rid` bigint(100) NOT NULL COMMENT 'role-id',
  `prid` bigint(100) NOT NULL COMMENT 'parent-role-id',
  UNIQUE KEY `rid` (`rid`),
  KEY `prid` (`prid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_perm_roles_parent` (`rid`, `prid`) VALUES
(2, 1),
(3, 2);

CREATE TABLE IF NOT EXISTS `cms_role_perms` (
  `rid` bigint(100) NOT NULL COMMENT 'Role ID',
  `pid` bigint(100) NOT NULL COMMENT 'Permission ID',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  KEY `rid` (`rid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_role_perms` (`rid`, `pid`, `value`) VALUES
(2, 3, 1),
(3, 4, 1),
(1, 5, 1),
(3, 6, 1),
(3, 7, 1),
(1, 8, 1);

CREATE TABLE IF NOT EXISTS `cms_sessions` (
  `id` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `userid` bigint(100) NOT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `remember` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `last_accessed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_settings` (
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_settings` (`name`, `value`) VALUES
('force_ssl', '0'),
('https_support', '0'),
('sitetitle', 'Shoutzor'),
('meta_charset', 'utf-8'),
('supportmail', 'contact@example.com'),
('meta_author', 'Jorin Vermeulen'),
('meta_description', 'Shoutzor Reloaded - Your LAN-Party radio'),
('maintenance', '0'),
('copyright', '&copy;2013 Jorin Vermeulen');

CREATE TABLE IF NOT EXISTS `cms_users` (
  `id` bigint(100) NOT NULL AUTO_INCREMENT COMMENT 'The ID of the user (auto generated)',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the family name of the user',
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the first name of the user',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the email of the user',
  `password` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'the encrypted password of the user',
  `joined` datetime NOT NULL COMMENT 'the timestamp from when the user account was made',
  `last_active` datetime NOT NULL COMMENT 'the timestamp from when the user last logged in',
  `status` tinyint(1) NOT NULL COMMENT 'active, banned, etc',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_users_verification_token` (
  `uid` bigint(100) NOT NULL,
  `token` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'a random md5 hash',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_user_login_attempts` (
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the client IP',
  `attempts` int(100) NOT NULL,
  `last_attempt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_user_perms` (
  `uid` bigint(100) NOT NULL COMMENT 'User ID',
  `pid` bigint(100) NOT NULL COMMENT 'Permission ID',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cms_user_roles` (
  `uid` bigint(100) NOT NULL,
  `rid` bigint(100) NOT NULL,
  KEY `uid` (`uid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `queue` (
  `track` bigint(20) NOT NULL,
  `user` bigint(100) DEFAULT NULL,
  `time_requested` datetime NOT NULL,
  UNIQUE KEY `track_2` (`track`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `queue_history` (
  `track` bigint(20) NOT NULL,
  `user` bigint(100) DEFAULT NULL,
  `time_played` datetime NOT NULL,
  `time_requested` datetime NOT NULL,
  KEY `track` (`track`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `track` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `length` varchar(255) NOT NULL,
  `crc` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crc` (`crc`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `track_album` (
  `track` bigint(20) NOT NULL,
  `album` int(10) NOT NULL,
  UNIQUE KEY `album` (`album`,`track`),
  UNIQUE KEY `track_2` (`track`,`album`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `track_artist` (
  `track` bigint(20) NOT NULL DEFAULT '0',
  `artist` bigint(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `track_2` (`track`,`artist`),
  KEY `artist` (`artist`,`track`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `album`
  ADD CONSTRAINT `album_ibfk_1` FOREIGN KEY (`artist`) REFERENCES `artist` (`id`);

ALTER TABLE `cms_perm_roles_parent`
  ADD CONSTRAINT `cms_perm_roles_parent_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `cms_perm_roles` (`id`),
  ADD CONSTRAINT `cms_perm_roles_parent_ibfk_2` FOREIGN KEY (`prid`) REFERENCES `cms_perm_roles` (`id`);

ALTER TABLE `cms_role_perms`
  ADD CONSTRAINT `cms_role_perms_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `cms_perm_roles` (`id`),
  ADD CONSTRAINT `cms_role_perms_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `cms_perms` (`id`);

ALTER TABLE `cms_users_verification_token`
  ADD CONSTRAINT `cms_users_verification_token_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `cms_users` (`id`);

ALTER TABLE `cms_user_perms`
  ADD CONSTRAINT `cms_user_perms_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `cms_users` (`id`),
  ADD CONSTRAINT `cms_user_perms_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `cms_perms` (`id`);

ALTER TABLE `cms_user_roles`
  ADD CONSTRAINT `cms_user_roles_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `cms_users` (`id`),
  ADD CONSTRAINT `cms_user_roles_ibfk_2` FOREIGN KEY (`rid`) REFERENCES `cms_perm_roles` (`id`);

ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`track`) REFERENCES `track` (`id`),
  ADD CONSTRAINT `queue_ibfk_2` FOREIGN KEY (`user`) REFERENCES `cms_users` (`id`);

ALTER TABLE `queue_history`
  ADD CONSTRAINT `queue_history_ibfk_1` FOREIGN KEY (`track`) REFERENCES `track` (`id`),
  ADD CONSTRAINT `queue_history_ibfk_2` FOREIGN KEY (`user`) REFERENCES `cms_users` (`id`);

ALTER TABLE `track_album`
  ADD CONSTRAINT `track_album_ibfk_1` FOREIGN KEY (`track`) REFERENCES `track` (`id`),
  ADD CONSTRAINT `track_album_ibfk_2` FOREIGN KEY (`album`) REFERENCES `album` (`id`);

ALTER TABLE `track_artist`
  ADD CONSTRAINT `track_artist_ibfk_1` FOREIGN KEY (`track`) REFERENCES `track` (`id`),
  ADD CONSTRAINT `track_artist_ibfk_2` FOREIGN KEY (`artist`) REFERENCES `artist` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
