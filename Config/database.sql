CREATE TABLE %DbName% (
	Id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	Name varchar(40) NOT NULL,
	Email varchar(50) NOT NULL,
	Title varchar(100) NOT NULL,
	Location varchar(180) NOT NULL,
	Description text NOT NULL,
	Date datetime NOT NULL,
	Views int(10) unsigned NOT NULL DEFAULT '0',
	Ip varchar(15) NOT NULL,
	Approved varchar(4) NOT NULL,
	PRIMARY KEY (Id),
	KEY Date (Date),
	KEY Email (Email, Name),
	KEY Approved (Approved)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;