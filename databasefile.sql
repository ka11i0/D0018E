CREATE TABLE produkt
			( Produktnamn  VARCHAR(20)	NOT NULL								,
			  Produkt_ID INT,
              Img_filsökväg VARCHAR(20) ,   				
              Pris	INT,
              Saldo	INT, 
              Produktbeskrivning VARCHAR(20), 						
PRIMARY KEY(Produkt_ID),
UNIQUE(Produktnamn)
);
CREATE TABLE konto
		    (Person_ID INT(20),
		    Namn VARCHAR(20) NOT NULL,
		    Lösenord VARCHAR(20) NOT NULL,
		    Födelsedag DATE,
		    Privilegie INT(1),
		    Mail VARCHAR(30) NOT NULL,
		    Stad VARCHAR (20),
		    Postnummer INT (10),
		    Address VARCHAR(30),
		    Telefonnummer INT(10),
PRIMARY KEY(Person_ID),
UNIQUE(Namn),
UNIQUE(Mail)
);
CREATE TABLE historik
			(Transaktion_ID INT ,
			 Person_ID INT,
			 Datum DATE NOT NULL,
			 Produkt_ID INT,
			 quantity INT,
FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID) ON DELETE CASCADE,
FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE,
PRIMARY KEY(Transaktion_ID, Produkt_ID)
);
CREATE TABLE varukorg
			(Person_ID INT,
			Produkt_ID INT,
			quantity INT,
FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID) ON DELETE CASCADE,
FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE,
PRIMARY KEY(Person_ID, Produkt_ID)
);

CREATE TABLE kommentarer
			(Kommentar_ID INT NOT NULL,
			Person_ID INT(20), 
			Produkt_ID INT NOT NULL,
			kommentar VARCHAR(100) NOT NULL,
			Datum DATE NOT NULL,
			FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID) ON DELETE SET NULL,
			FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE,
			PRIMARY KEY(Kommentar_ID)
);
);

CREATE TABLE rating
			(
			Person_ID INT(20)NOT NULL , 
			Produkt_ID INT NOT NULL,
			rating int(1) NOT NULL,
			FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID) ON DELETE CASCADE,
			FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE,
			PRIMARY KEY(Person_ID,Produkt_ID)
);

CREATE TABLE kampanj
	(
	Kampanj_ID INT(10) NOT NULL, 
	Produkt_ID INT(10) NOT NULL,
	Procent INT(10) NOT NULL,
	Start DATE NOT NULL, 
	Slut DATE NOT NULL, 
	FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE,
	PRIMARY KEY(Kampanj_ID)
);