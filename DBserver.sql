-- Authors: xkozub09, xkrajc26, xkucer0v
-- Date: 04.10.2024
-- Projekt: IIS - Zelny trh
-- architecture layer: DB server


SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Uzivatel;
DROP TABLE IF EXISTS Kategorie;
DROP TABLE IF EXISTS Nabidka;
DROP TABLE IF EXISTS Objednavka;
DROP TABLE IF EXISTS Atribut;
DROP TABLE IF EXISTS HodnotaAtributu;
DROP TABLE IF EXISTS Relace_objednavka_nabidka;
DROP TABLE IF EXISTS Relace_nabidka_atribut;
DROP TABLE IF EXISTS Hodnoceni;
SET FOREIGN_KEY_CHECKS = 1;



-- ____Definice Tabulek____

-- Tabulka Uzivatel
CREATE TABLE Uzivatel (
    prihlasovaci_jmeno VARCHAR(100) PRIMARY KEY,
    heslo VARCHAR(100) NOT NULL,
    jmeno VARCHAR(100) NOT NULL,
    urole ENUM('Admin', 'Moderator', 'Farmar', 'Zakaznik'),
    email VARCHAR(100) UNIQUE NOT NULL,
    datum_narozeni DATE,
    adresa VARCHAR (100),
    dalsi_osobni_udaje VARCHAR(100)
);

CREATE TABLE Kategorie (
    id_kategorie INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id_kategorie),

    nazev VARCHAR(100),
    popis VARCHAR(100),
    foto BLOB,
    schvaleno ENUM('ANO', 'NE'),

    parent INT, -- cizí klíč odkaz do nadřazené kategorie
    FOREIGN KEY (parent) REFERENCES Kategorie(id_kategorie) ON DELETE CASCADE,

    navrhl VARCHAR(100),
    FOREIGN KEY (navrhl) REFERENCES Uzivatel(prihlasovaci_jmeno) ON DELETE CASCADE,

    schvalil VARCHAR(100),
    FOREIGN KEY (schvalil) REFERENCES Uzivatel(prihlasovaci_jmeno) ON DELETE CASCADE
);


CREATE TABLE Nabidka (
    id_nabidky INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id_nabidky),
    
    nazev VARCHAR(100) NOT NULL,
    popis VARCHAR(100),
    misto_puvodu VARCHAR(100),
    mnozstvi INT NOT NULL,
    cena INT NOT NULL,
    druh_ceny ENUM('HMOTNOST', 'KUSY'),
    trvanlivost DATE,
    samozber ENUM('ANO', 'NE'),
    lokalita VARCHAR(100),
    cas_od DATE,
    cas_do DATE,
    schvaleno ENUM('ANO', 'NE'),
    
    id_kategorie INT,
    FOREIGN KEY (id_kategorie) REFERENCES Kategorie(id_kategorie) ON DELETE CASCADE,

    vlastnik VARCHAR(100),
    FOREIGN KEY (vlastnik) REFERENCES Uzivatel(prihlasovaci_jmeno) ON DELETE CASCADE,

    CONSTRAINT kontrola_rozsahu_ceny CHECK (cena >= 0),
    CONSTRAINT kontrola_rozsahu_mnozstvi CHECK (mnozstvi >= 0)
);

CREATE TABLE Hodnoceni (
    id_hodnoceni INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id_hodnoceni),
    
    id_nabidky INT NOT NULL,
    FOREIGN KEY (id_nabidky) REFERENCES Nabidka(id_nabidky) ON DELETE CASCADE,
    
    zakaznik VARCHAR(100) NOT NULL,
    FOREIGN KEY (zakaznik) REFERENCES Uzivatel(prihlasovaci_jmeno) ON DELETE CASCADE,
    
    hodnoceni TINYINT NOT NULL CHECK (hodnoceni BETWEEN 1 AND 5), -- Hodnocení 1-5
    komentar TEXT, -- Volitelný komentář
    
    datum_hodnoceni DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabulka Objednavka
CREATE TABLE Objednavka (
    id_objednavky INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id_objednavky),

    celkova_cena INT NOT NULL,
    stav ENUM('vytvorena', 'zaplacena', 'zpracovana', 'zaslany', 'dorucena', 'prijata', 'stornovana', 'reklamace'),
    datum_vytvoreni DATE,
    datum_platby DATE,
    datum_vyrizeni DATE,
    datum_prebrani DATE,
    druh_platby ENUM('prevod', 'karta', 'paypal', 'hotovost'),
    cislo_uctu VARCHAR(22),

    vlastnik VARCHAR(100),
    FOREIGN KEY (vlastnik) REFERENCES Uzivatel(prihlasovaci_jmeno) ON DELETE CASCADE,

    CONSTRAINT kontrola_rozsahu_ceny_2 CHECK (celkova_cena >= 0)
);


-- Vztahova mnozina obsahuje s parametry pocet a cena
CREATE TABLE Relace_objednavka_nabidka (
    id_objednavky INT,
    id_nabidky INT,
    
    -- parametry
    objem INT, -- buď hmotnost v kg nebo počet v ks
    cena INT,

    CONSTRAINT kontrola_objemu CHECK (objem >= 0),
    CONSTRAINT kontrola_ceny CHECK (cena >= 0),

    PRIMARY KEY (id_objednavky, id_nabidky),

    FOREIGN KEY (id_objednavky) REFERENCES Objednavka(id_objednavky) ON DELETE CASCADE,
    FOREIGN KEY (id_nabidky) REFERENCES Nabidka(id_nabidky) ON DELETE CASCADE
);


-- Atribut table without id_hodnoty
CREATE TABLE Atribut (
    id_atributu INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id_atributu),
    nazev VARCHAR(100) NOT NULL
);

-- HodnotaAtributu table links to Atribut
CREATE TABLE HodnotaAtributu (
    id_hodnoty INT NOT NULL AUTO_INCREMENT,
    id_atributu INT NOT NULL,
    hodnota VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_hodnoty),
    FOREIGN KEY (id_atributu) REFERENCES Atribut(id_atributu) ON DELETE CASCADE
);

-- Upravená tabulka Relace_nabidka_atribut
-- Updated Relace_nabidka_atribut table
CREATE TABLE Relace_nabidka_atribut (
    id_atributu INT,
    id_nabidky INT,
    id_hodnoty INT, -- Reference to HodnotaAtributu
    
    PRIMARY KEY (id_atributu, id_nabidky),
    FOREIGN KEY (id_atributu) REFERENCES Atribut(id_atributu) ON DELETE CASCADE,
    FOREIGN KEY (id_nabidky) REFERENCES Nabidka(id_nabidky) ON DELETE CASCADE,
    FOREIGN KEY (id_hodnoty) REFERENCES HodnotaAtributu(id_hodnoty) ON DELETE CASCADE -- Ensure foreign key constraint
);
-- ____Konec Definic Tabulek____

INSERT INTO Atribut (id_atributu, nazev)
VALUES
(1, 'Barva'),
(2, 'Velikost'),
(3, 'Chuť');

INSERT INTO HodnotaAtributu (id_atributu, hodnota)
VALUES 
    (1, 'modrá'), -- Přidání nové barvy
    (1, 'červená'), -- Přidání nové barvy
    (1, 'žlutá'), -- Přidání nové barvy
    (2, 'malá'), -- Přidání nové velikosti
    (2, 'střední'), -- Přidání nové velikosti
    (2, 'velká'), -- Přidání nové velikosti
    (3, 'sladká'), -- Přidání nové chuti
    (3, 'kyselá'), -- Přidání nové chuti
    (3, 'hořká'); -- Přidání nové chuti


-- ____Vkladani ukazkovych dat____
INSERT INTO Uzivatel (prihlasovaci_jmeno, heslo, jmeno, urole, email, datum_narozeni, adresa, dalsi_osobni_udaje)
VALUES 
('farmer1', 'heslo123', 'Jan Novak', 'Farmar', 'jan.novak@example.com', '1980-05-15', NULL, 'Farmer from Brno'),
('farmer2', 'heslo456', 'Petr Svec', 'Farmar', 'petr.svec@example.com', '1975-10-10', NULL, 'Farmer from Prague'),
('zakaznik1', 'heslo789', 'Marta Maly', 'Zakaznik', 'marta.maly@example.com', '1992-07-23', NULL, 'Customer from Ostrava'),
('admin1', 'hesloadmin', 'Jana Hrda', 'Admin', 'jana.hrda@example.com', '1985-03-11', NULL, 'Admin of the system');

INSERT INTO Kategorie (id_kategorie, nazev, popis, foto, schvaleno, parent, navrhl, schvalil)
VALUES 
(1, 'Zelenina', 'Čerstvá zelenina od farmářů', NULL, 'ANO', NULL, 'farmer1', 'admin1'),
(2, 'Ovoce', 'Čerstvé ovoce ze sadů', NULL, 'ANO', NULL, 'farmer2', 'admin1'),
(3, 'Mléčné výrobky', 'Sýry, mléko a další produkty', NULL, 'ANO', NULL, 'farmer1', 'admin1');

INSERT INTO Nabidka (id_nabidky, nazev, popis, misto_puvodu, mnozstvi, cena, druh_ceny, trvanlivost, samozber, lokalita, cas_od, cas_do, schvaleno, id_kategorie, vlastnik)
VALUES
(1, 'Brambory', 'Čerstvé brambory z pole', 'Brno', 100, 20, 'HMOTNOST', '2024-12-31', 'NE', 'Brno', NULL, NULL, 'ANO', 1, 'farmer1'),
(2, 'Jablka', 'Zralá jablka ze sadu', 'Praha', 150, 25, 'HMOTNOST', '2024-11-30', 'ANO', 'Praha', NULL, NULL, 'ANO', 2, 'farmer2'),
(3, 'Sýr gouda', 'Domácí gouda', 'Brno', 50, 80, 'KUSY', '2025-01-15', 'NE', 'Brno', NULL, NULL, 'ANO', 3, 'farmer1');

INSERT INTO Objednavka (id_objednavky, celkova_cena, stav, datum_vytvoreni, datum_platby, datum_vyrizeni, datum_prebrani, druh_platby, cislo_uctu, vlastnik)
VALUES 
(1, 500, 'vytvorena', '2024-10-10', NULL, NULL, NULL, 'prevod', '1234567890/0100', 'zakaznik1'),
(2, 2000, 'zaplacena', '2024-10-11', '2024-10-12', NULL, NULL, 'karta', '9876543210/0300', 'zakaznik1');


INSERT INTO Relace_objednavka_nabidka (id_objednavky, id_nabidky, objem, cena)
VALUES
(1, 1, 10, 200),
(1, 3, 5, 400),
(2, 2, 20, 500);

INSERT INTO Relace_nabidka_atribut (id_atributu, id_nabidky)
VALUES
(1, 1),
(2, 1),
(3, 2);


INSERT INTO Hodnoceni (id_hodnoceni, id_nabidky, zakaznik, hodnoceni, komentar, datum_hodnoceni)
VALUES
    (1, 1, 'zakaznik1', 5, 'Dobré brambory', '2024-10-10 10:00:00'),
    (2, 2, 'zakaznik1', 4, 'Jablka byla výborná', '2024-10-11 10:00:00'),
    (3, 3, 'zakaznik1', 3, 'Sýr byl průměrný', '2024-10-12 10:00:00');



-- ____Konec Vkladani ukazkovych dat____


-- ____Definice Selectu____

-- Kontrolni selecty

SELECT * FROM Uzivatel;
SELECT * FROM Kategorie;
SELECT * FROM Nabidka;
SELECT * FROM Objednavka;
SELECT * FROM Atribut;
SELECT * FROM Relace_objednavka_nabidka;
SELECT * FROM Relace_nabidka_atribut;


-- Filtrovani podle jmena farmare

-- Filtrovani podle jmena farmare razeni podle mnozstvi
SELECT * 
FROM Nabidka
WHERE vlastnik = 'farmer1'
ORDER BY mnozstvi ASC; -- DESC pro sestupne

-- Filtrovani podle jmena farmare razeni podle ceny
SELECT * 
FROM Nabidka
WHERE vlastnik = 'farmer1' AND druh_ceny = 'HMOTNOST'  -- popr. 'KUSY' 
ORDER BY cena ASC; -- DESC pro sestupne

-- Filtrovani podle jmena farmare razeni podle trvanlivosti
SELECT * 
FROM Nabidka
WHERE vlastnik = 'farmer1'
ORDER BY trvanlivost ASC; -- DESC pro sestupne


-- Filtrovani podle kategorie

-- Filtrovani podle kategorie razeni podle ceny
SELECT * 
FROM Nabidka
WHERE id_kategorie = (SELECT id_kategorie FROM Kategorie WHERE nazev = 'nazev_kategorie') AND druh_ceny = 'HMOTNOST'  -- popr. 'KUSY' 
ORDER BY cena ASC; -- DESC pro sestupne

-- Filtrovani podle kategorie razeni podle mnozstvi
SELECT * 
FROM Nabidka
WHERE id_kategorie = (SELECT id_kategorie FROM Kategorie WHERE nazev = 'nazev_kategorie')
ORDER BY mnozstvi ASC; -- DESC pro sestupne

-- Filtrovani podle kategorie razeni podle trvanlivosti
SELECT * 
FROM Nabidka
WHERE id_kategorie = (SELECT id_kategorie FROM Kategorie WHERE nazev = 'nazev_kategorie')
ORDER BY trvanlivost ASC; -- DESC pro sestupne

-- ____Konec Definic Selectu____
