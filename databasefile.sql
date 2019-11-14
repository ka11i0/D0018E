CREATE TABLE produkt
			( Produktnamn  VARCHAR(20)	NOT NULL								,
			  Produkt_ID INT 				    ,
              Img_filsökväg VARCHAR(20) ,   				
              Pris	INT 						,
              Saldo	INT	 		, 
              Produktbeskrivning VARCHAR(20), 						
PRIMARY KEY (Produkt_ID)
);
CREATE TABLE konto
		    (Person_ID INT ,
		     Namn VARCHAR(20) NOT NULL,
		     Lösenord VARCHAR(20) NOT NULL,
		     Datum_konto DATE ,
PRIMARY KEY(Person_ID),
UNIQUE(Namn)
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
FOREIGN KEY(Produkt_varu) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE
);

CREATE TABLE kommentarer
			(Person_ID INT, 
			Produkt_ID INT,
			kommentar VARCHAR(100),
			FOREIGN KEY(Person_ID) REFERENCES konto(Person_ID)ON DELETE SET NULL,
			FOREIGN KEY(Produkt_ID) REFERENCES produkt(Produkt_ID) ON DELETE CASCADE
);

