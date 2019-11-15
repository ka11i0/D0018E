CREATE TABLE produkt
			( Produktnamn  VARCHAR(20)	NOT NULL								,
			  Produkt_ID INT,
              Img_filsökväg VARCHAR(20) ,   				
              Pris	INT,
              Saldo	INT, 
              Produktbeskrivning VARCHAR(20), 						
PRIMARY KEY (Produkt_ID)
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
			 Konto_hist INT,
			 Datum DATE NOT NULL,
			 Prod_hist INT,
FOREIGN KEY(Konto_hist) REFERENCES konto(Person_ID) ON DELETE CASCADE,
FOREIGN KEY(Prod_hist) REFERENCES produkt(Produkt_ID) ON DELETE SET NULL
);
CREATE TABLE vaurkorg
			(Konto_varu INT,
			 Produkt_varu INT,
FOREIGN KEY(Konto_varu) REFERENCES konto(Person_ID) ON DELETE CASCADE,
FOREIGN KEY(Produkt_varu) REFERENCES produkt(Produkt_ID)
);

CREATE TABLE kommentarer
			(Person_ID INT NOT NULL, 
			Produkt_ID INT NOT NULL,
			kommentar VARCHAR(100) NOT NULL,
			Datum DATE NOT NULL,
			FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID),
			FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID)
);
FOREIGN KEY(Produkt_varu) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE
);