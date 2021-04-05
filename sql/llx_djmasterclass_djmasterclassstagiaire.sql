-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_djmasterclass_djmasterclassstagiaire(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	amount double DEFAULT NULL, 
	date_creation datetime NOT NULL, 
	email varchar(64) NOT NULL, 
	lastname varchar(32) NOT NULL, 
	firstname varchar(32) NOT NULL, 
	phone varchar(16) NOT NULL, 
	token varchar(32) NOT NULL, 
	fk_djmasterclasssession integer NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
