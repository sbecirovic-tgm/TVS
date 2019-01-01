-- Datum: 12.11.2018
-- Version 2.0
-- Autor: Sebastian Wahl @ 4AHIT
-- Zweck: Tokenverwaltungssystem

DROP DATABASE IF EXISTS tvs_datenbank;
CREATE DATABASE tvs_datenbank;
USE tvs_datenbank;

CREATE TABLE wildcard (
		id INTEGER NOT NULL AUTO_INCREMENT,
		beschreibung TEXT,
		PRIMARY KEY ( id )
) ENGINE = INNODB;

CREATE TABLE superuser (
		kuerzel VARCHAR(255),
		lName VARCHAR(255),
		PRIMARY KEY ( kuerzel )
) ENGINE = INNODB;

CREATE TABLE award (
		name VARCHAR(255),
		tokenLimit INTEGER,

		PRIMARY KEY (name)
) ENGINE = INNODB;


CREATE TABLE lehrer (
		kuerzel VARCHAR(255),
		lName VARCHAR(255),
		skuerzel VARCHAR(255),

		PRIMARY KEY ( kuerzel ),
		FOREIGN KEY ( skuerzel )
		REFERENCES superuser(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE erlaubnis (
		lKuerzel VARCHAR(255),
		aName VARCHAR(255),

		PRIMARY KEY ( lKuerzel, aName ),
		FOREIGN KEY (lKuerzel)
		REFERENCES lehrer(kuerzel)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
		FOREIGN KEY (aName)
		REFERENCES award(name)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE schueler (
		kuerzel VARCHAR(255),
		sName VARCHAR(255),

		PRIMARY KEY ( kuerzel)
) ENGINE = INNODB;

CREATE TABLE leistung (
		aName VARCHAR(255),
		sKuerzel VARCHAR(255),
		tokenAnzahl INTEGER,
		saisonNummer INTEGER,
		PRIMARY KEY (aName, sKuerzel, saisonNummer),

		FOREIGN KEY (aName)
		REFERENCES award(name)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
		FOREIGN KEY (sKuerzel)
		REFERENCES schueler(kuerzel)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE zuordnung (
		wID INTEGER,
		skuerzel VARCHAR(255),

		PRIMARY KEY (wID, skuerzel),

		FOREIGN KEY ( wID )
		REFERENCES wildcard(id)
		ON UPDATE CASCADE
		On DELETE CASCADE,
		FOREIGN KEY ( skuerzel )
		REFERENCES schueler(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE auszeichnung (
		datum DATE,
		zeit TIME,
		skuerzel VARCHAR(255),
		awardName VARCHAR(255),
		saisonNummer INTEGER,
		PRIMARY KEY ( datum, zeit, skuerzel, saisonNummer ),
		FOREIGN KEY ( skuerzel )
		REFERENCES schueler(kuerzel)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
		FOREIGN KEY ( awardName )
		REFERENCES award(name)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = INNODB;


CREATE TABLE event (
		name VARCHAR(255),
		datum DATE,
		superKuerzel VARCHAR(255),
		lKuerzel VARCHAR(255),
		aName VARCHAR(255),

		beschreibung TEXT,
		PRIMARY KEY ( name, datum, aName),

		FOREIGN KEY ( aName )
		REFERENCES award(name)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

		FOREIGN KEY ( superKuerzel )
		REFERENCES superuser(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE,

		FOREIGN KEY ( lKuerzel )
		REFERENCES lehrer(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE unterkategorie (
		name VARCHAR(255),

		eName VARCHAR(255),
		aName VARCHAR(255),
		eDatum DATE,
		wID INTEGER,

		tokenAnzahl INTEGER,
		beschreibung TEXT,

		PRIMARY KEY ( name, eName, eDatum, aName),

		FOREIGN KEY (eName, eDatum, aName)
		REFERENCES event(name, datum, aName)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

		FOREIGN KEY ( wID )
		REFERENCES wildcard(id)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE anfrage (
		id INTEGER NOT NULL AUTO_INCREMENT,
		datum DATE,
		zeit TIME,
		skuerzel VARCHAR(255),

		-- FOREIGN KEYs
		aName VARCHAR(255), -- award name
		superkuerzel VARCHAR(255),
		lehrerKuerzel VARCHAR(255),

		eName VARCHAR(255),
		eDatum DATE,
		untName VARCHAR(255),

		tokenAnzahl INTEGER NOT NULL,
		tokenAnzahlNeu INTEGER, -- Wird nur vom Admin gesetzt, also ist am Anfang null
		beschreibung TEXT,
		betreff VARCHAR(255),
		wirdBewilligt BOOLEAN, -- NULL wenn noch nichts eingetragen wurde
		kommentar TEXT, -- Admin schreibt ein Kommentar, anfangs NULL
		PRIMARY KEY ( id, datum, zeit, skuerzel ),

		FOREIGN KEY ( skuerzel )
		REFERENCES schueler(kuerzel)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

		FOREIGN KEY ( aName )
		REFERENCES award(name)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

		FOREIGN KEY ( superkuerzel )
		REFERENCES superuser(kuerzel)
		ON UPDATE CASCADE
		ON DELETE CASCADE,

		FOREIGN KEY ( lehrerKuerzel )
		REFERENCES lehrer(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE,

		FOREIGN KEY (untName, eName, eDatum)
		REFERENCES unterkategorie(name, eName, eDatum)
		ON UPDATE CASCADE
		On DELETE CASCADE

) ENGINE = INNODB;

-- Saison Länge
CREATE TABLE saisonEinstellung (
       startJahr INTEGER,
       startDatumWSem DATE,
       endDatumWSem DATE,
	   startDatumSSem DATE,
	   endDatumSSem DATE,

	   PRIMARY KEY ( startJahr )
) ENGINE = INNODB;

-- Koppensteiner als Superuser:
insert into superuser( kuerzel, lName)
values ('gkoppensteiner', "G. Koppensteiner");

-- Saison Länge default YYYY-MM-DD
insert into saisonEinstellung ( startJahr, startDatumWSem, endDatumWSem, startDatumSSem, endDatumSSem)
values ( '2018', '2018-9-1', '2019-2-1', '2019-2-2', '2019-7-31' );


-- VALUES zum testen

insert into award( name, tokenLimit )
values ('Test-Award1', '15');
insert into award( name, tokenLimit )
values ('Test-Award2', '15');
insert into award( name, tokenLimit )
values ('Test-Award3', '15');

insert into schueler( kuerzel, sName )
values ('swahl', 'Sebastian Wahl');


insert into schueler( kuerzel, sName )
values ('fgavric', 'Filip Gavric');



insert into event(name, datum, superKuerzel, lKuerzel, aName)
values ('Test event', CURDATE(), Null, Null, 'Test-Award2');

insert into unterkategorie(name, eName, aName, eDatum, wID)
values ('Bericht', 'Test event', 'Test-Award2', CURDATE(), NULL);