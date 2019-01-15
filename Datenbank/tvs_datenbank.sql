-- Datum: 12.11.2018
-- Version 2.0
-- Autor: Sebastian Wahl @ 4AHIT
-- Zweck: Tokenverwaltungssystem

DROP DATABASE IF EXISTS tvs_datenbank;
CREATE DATABASE tvs_datenbank;
USE tvs_datenbank;

CREATE TABLE wildcard (
		id INTEGER NOT NULL AUTO_INCREMENT,
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
		wID INTEGER,

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
		On DELETE CASCADE,

		FOREIGN KEY ( wID )
		REFERENCES wildcard( id )
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE unterkategorie (
		name VARCHAR(255),

		eName VARCHAR(255),
		aName VARCHAR(255),
		eDatum DATE,

		tokenAnzahl INTEGER,
		beschreibung TEXT,

		PRIMARY KEY ( name, eName, eDatum, aName),

		FOREIGN KEY (eName, eDatum, aName)
		REFERENCES event(name, datum, aName)
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
values ('kopg', "Gottfried Koppensteiner");

-- Saison Länge default YYYY-MM-DD
insert into saisonEinstellung ( startJahr, startDatumWSem, endDatumWSem, startDatumSSem, endDatumSSem)
values ( '2018', '2018-9-1', '2019-2-1', '2019-2-2', '2019-7-31' );



insert into schueler( kuerzel, sName )
values ('swahl', 'Sebastian Wahl');

insert into schueler( kuerzel, sName )
values ('fgavric', 'Filip Gavric');


-- Lehrer inserts
insert into lehrer values ( 'atat', 'Tatjana Atanasoska', null);
insert into lehrer values ( 'bacp', 'Peter Bacsics', null);
insert into lehrer values ( 'bauw0', 'Wolfgang Baumgarnter', null);
insert into lehrer values ( 'borm', 'Michael Borko', null);
insert into lehrer values ( 'brah', 'Hans Brabenetz', null);
insert into lehrer values ( 'brec', 'Christoph Brein', null);
insert into lehrer values ( 'clac', 'Christian Claucig', null);
insert into lehrer values ( 'dold', 'Dominik Dolezal', null);
insert into lehrer values ( 'eftr', 'Roshanak Efthekhar', null);
insert into lehrer values ( 'ehrn', 'Natascha Ehrenberger', null);
insert into lehrer values ( 'fejw', 'Wolgang Fejan', null);
insert into lehrer values ( 'fisb', 'Bettina Fischer', null);
insert into lehrer values ( 'fism', 'Michael Fischer', null);
insert into lehrer values ( 'fued', 'Dominik Fürnsin', null);
insert into lehrer values ( 'gras', 'Stefanie Gradnitzer-Pekovits', null);
insert into lehrer values ( 'gram', 'Michael Graf', null);
insert into lehrer values ( 'guem', 'Michael Günthör', null);
insert into lehrer values ( 'hohd', 'Dieter Hohenwarter', null);
insert into lehrer values ( 'hoek', 'Kai Höher', null);
insert into lehrer values ( 'janj', 'Jennifer Janik', null);
insert into lehrer values ( 'jire', 'Eugen Robert Jiresch', null);
insert into lehrer values ( 'kolk', 'Kerstin Kollitsch', null);
insert into lehrer values ( 'krah0', 'Helmut Kraus', null);
insert into lehrer values ( 'kruc', 'Christian Kruisz', null);
insert into lehrer values ( 'marm', 'Michael Martinides', null);
insert into lehrer values ( 'mict', 'Thomas Micheler', null);
insert into lehrer values ( 'nimw', 'Wolfgang Nimmervoll', null);
insert into lehrer values ( 'norh', 'Henrik Nordin', null);
insert into lehrer values ( 'pamt', 'Thomas Pamperl', null);
insert into lehrer values ( 'paum', 'Manfred Paulitsch', null);
insert into lehrer values ( 'pawd', 'Daniela Pawelak', null);
insert into lehrer values ( 'pesa', 'Alfred Pesek', null);
insert into lehrer values ( 'posa', 'Alexandra Posekany', null);
insert into lehrer values ( 'posa0', 'Alexander Poszvek', null);
insert into lehrer values ( 'radj0', 'Johann Radatz', null);
insert into lehrer values ( 'rafw', 'Walter Rafeiner-Magor', null);
insert into lehrer values ( 'reim2', 'Monika Reichart', null);
insert into lehrer values ( 'rosc', 'Christoph Roschger', null);
insert into lehrer values ( 'sabp', 'Peter Schabasser', null);
insert into lehrer values ( 'sabm', 'Markus Schabel', null);
insert into lehrer values ( 'soeb', 'Barbara Schoell', null);
insert into lehrer values ( 'sult', 'Tamara Schuller', null);
insert into lehrer values ( 'stow0', 'Wolfgang Stoiber', null);
insert into lehrer values ( 'thut', 'Thomas Thun', null);
insert into lehrer values ( 'tree', 'Erich Trenner', null);
insert into lehrer values ( 'tred', 'Denis Trenner', null);
insert into lehrer values ( 'trif', 'Franz Tripolt', null);
insert into lehrer values ( 'tues', 'Salih Tuerkmen', null);
insert into lehrer values ( 'umaa', 'Adeel Umar', null);
insert into lehrer values ( 'urbb', 'Brigitte Urbarz', null);
insert into lehrer values ( 'vikc', 'Carolin Vikoler', null);
insert into lehrer values ( 'vitl', 'Lisa Vittori', null);
insert into lehrer values ( 'vocm', 'Mathias Vock', null);
insert into lehrer values ( 'webm', 'Michael Weber', null);
insert into lehrer values ( 'weij', 'Johann Weiser', null);
insert into lehrer values ( 'wile', 'Elisabeth Wildling', null);
insert into lehrer values ( 'wimr', 'Robert Wimberger', null);
insert into lehrer values ( 'zaks', 'Stefan Zakall', null);

-- Award inserts
INSERT INTO award VALUES ('Pulitzer', 15);
INSERT INTO award VALUES ('Editor', 15);
INSERT INTO award VALUES ('Favorite', 15);
INSERT INTO award VALUES ('Architect', 15);
-- Der wird speziell behandelt (wenn alle 4 dann auch den)
INSERT INTO award VALUES ('Spirit of HIT', 15);
