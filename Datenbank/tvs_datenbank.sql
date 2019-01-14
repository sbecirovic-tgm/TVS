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


-- test event mit unterketegorien und wildcard
insert into event(name, datum, superKuerzel, lKuerzel, aName, wID, beschreibung)
values ('Test event', '2019-1-2', Null, Null, 'Test-Award2', NULL, 'Dieses Event testet alle Funktionen!');

insert into unterkategorie(name, eName, aName, eDatum, tokenAnzahl, beschreibung)
values ('Bericht', 'Test event', 'Test-Award2', '2019-1-2', 2, 'Du hast einen Bericht geschrieben');

insert into unterkategorie(name, eName, aName, eDatum, tokenAnzahl, beschreibung)
values ('Bilder aufgenommen', 'Test event', 'Test-Award2', '2019-1-2', 1, 'Du hast Bilder aufgenommen' );

-- Wildcard
insert into wildcard (beschreibung)
values ('Hier können sich nur Leute aus der 4. Klasse anmelden');
-- hinzufügen zum event
update event set wID = (select id from wildcard order by id desc limit 1);
-- zuordnung zu den schülern
insert into zuordnung ( wID, skuerzel )
values ( 1, 'swahl');

-- test event2 mit unterketegorien und wildcard
insert into event(name, datum, superKuerzel, lKuerzel, aName, wID, beschreibung)
values ('Test event 2', '2019-1-15', Null, Null, 'Test-Award1', NULL, 'Dieses Event testet alle Funktionen!');

insert into unterkategorie(name, eName, aName, eDatum, tokenAnzahl, beschreibung)
values ('Bericht', 'Test event 2', 'Test-Award1', '2019-1-15', 2, 'Du hast einen Bericht geschrieben');


-- Lehrer inserts
insert into lehrer values ( 'atat', 'Atanasoska Tatjana', null);
insert into lehrer values ( 'bacp', 'Bacsics Peter', null);
insert into lehrer values ( 'bauw0', 'Baumgartner Wolfgang', null);
insert into lehrer values ( 'borm', 'Borko Michael', null);
insert into lehrer values ( 'brah', 'Brabenetz Hans', null);
insert into lehrer values ( 'brec', 'Brein Christoph', null);
insert into lehrer values ( 'clac', 'Claucig Christian', null);
insert into lehrer values ( 'dold', 'Dolezal Dominik', null);
insert into lehrer values ( 'eftr', 'Efthekhar Roshanak', null);
insert into lehrer values ( 'ehrn', 'Ehrenberger Natascha', null);
insert into lehrer values ( 'fejw', 'Fejan Wolfgang', null);
insert into lehrer values ( 'fisb', 'Fischer Bettina', null);
insert into lehrer values ( 'fism', 'Fischer Michael', null);
insert into lehrer values ( 'fued', 'Fürnsin Dominik', null);
insert into lehrer values ( 'gras', 'Gradnitzer-Pekovits Stefanie', null);
insert into lehrer values ( 'gram', 'Graf Michael', null);
insert into lehrer values ( 'guem', 'Günthör Michael', null);
insert into lehrer values ( 'hohd', 'Hohenwarter Dieter', null);
insert into lehrer values ( 'hoek', 'Höher Kai', null);
insert into lehrer values ( 'janj', 'Janik Jennifer', null);
insert into lehrer values ( 'jire', 'Jiresch Eugen Robert', null);
insert into lehrer values ( 'kolk', 'Kollitsch Kerstin', null);
insert into lehrer values ( 'kopg', 'Koppensteiner Gottfried', null);
insert into lehrer values ( 'krah0', 'Kraus Helmut', null);
insert into lehrer values ( 'kruc', 'Kruisz Christian', null);
insert into lehrer values ( 'marm', 'Martinides Michael', null);
insert into lehrer values ( 'mict', 'Micheler Thomas', null);
insert into lehrer values ( 'nimw', 'Nimmervoll Wolfgang', null);
insert into lehrer values ( 'norh', 'Nordin Henrik', null);
insert into lehrer values ( 'pamt', 'Pamperl Thomas', null);
insert into lehrer values ( 'paum', 'Paulitsch Manfred', null);
insert into lehrer values ( 'pawd', 'Pawelak Daniela', null);
insert into lehrer values ( 'pesa', 'Pesek Alfred', null);
insert into lehrer values ( 'posa', 'Posekany Alexandra', null);
insert into lehrer values ( 'posa0', 'Poszvek Alexander', null);
insert into lehrer values ( 'radj0', 'Radatz Johann', null);
insert into lehrer values ( 'rafw', 'Rafeiner-Magor', null);
insert into lehrer values ( 'reim2', 'Reichart Monika', null);
insert into lehrer values ( 'rosc', 'Roschger Christoph', null);
insert into lehrer values ( 'sabp', 'Schabasser Peter', null);
insert into lehrer values ( 'sabm', 'Schabel Markus', null);
insert into lehrer values ( 'soeb', 'Schoell Barbara', null);
insert into lehrer values ( 'sult', 'Schuller Tamara', null);
insert into lehrer values ( 'stow0', 'Stoiber Wolfgang', null);
insert into lehrer values ( 'thut', 'Thun Thomas', null);
insert into lehrer values ( 'tree', 'Trenner Erich', null);
insert into lehrer values ( 'tred', 'Trenner Denis', null);
insert into lehrer values ( 'trif', 'Tripolt Franz', null);
insert into lehrer values ( 'tues', 'Tuerkmen Salih', null);
insert into lehrer values ( 'umaa', 'Umar Adeel', null);
insert into lehrer values ( 'urbb', 'Urbarz Brigitte', null);
insert into lehrer values ( 'vikc', 'Vikoler Carolin', null);
insert into lehrer values ( 'vitl', 'Vittori Lisa', null);
insert into lehrer values ( 'vocm', 'Vock Mathias', null);
insert into lehrer values ( 'webm', 'Weber Michael', null);
insert into lehrer values ( 'weij', 'Weiser Johann', null);
insert into lehrer values ( 'wile', 'Wildling Elisabeth', null);
insert into lehrer values ( 'wimr', 'Wimberger Robert', null);
insert into lehrer values ( 'zaks', 'Zakall Stefan', null);
