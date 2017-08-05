CREATE TABLE IF NOT EXISTS `#__mcomments_<id>` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(255) NOT NULL,
	`message` mediumtext NOT NULL,
	`parent` int(11) DEFAULT 0,
	`branchId` int(11) DEFAULT 0,
	`utime` int(11) DEFAULT 0,
	`level` int(11) DEFAULT 0,
	`state` int(11) DEFAULT 1,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__mcomments_ids` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`home` int(1) DEFAULT 0,
	`path` varchar(255) NOT NULL,
	`table_name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__mcomments_last` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`mcid` int(11) NOT NULL,
	`table_name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

DROP TABLE `#_mcomments_ids`;

-- Inline
CREATE TABLE IF NOT EXISTS `#__mcomments_<id>` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) NOT NULL, `message` mediumtext NOT NULL, `parent` int(11) DEFAULT 0, `branchId` int(11) DEFAULT 0, `utime` int(11) DEFAULT 0, `level` int(11) DEFAULT 0, `state` int(11) DEFAULT 1, PRIMARY KEY (`id`) );

CREATE TABLE IF NOT EXISTS `#__mcomments_ids` ( `id` int(11) NOT NULL AUTO_INCREMENT, `home` int(1) DEFAULT 0, `path` varchar(255) NOT NULL, `table_name` varchar(255) NOT NULL, PRIMARY KEY (`id`) );

CREATE TABLE IF NOT EXISTS `#__mcomments_last` ( `id` int(11) NOT NULL AUTO_INCREMENT, `mcid` int(11) NOT NULL, `table_name` varchar(255) NOT NULL, PRIMARY KEY (`id`) );