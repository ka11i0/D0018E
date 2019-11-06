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
FOREIGN KEY(Konto_hist) REFERENCES konto(Person_ID),
FOREIGN KEY(Prod_hist) REFERENCES produkt(Produkt_ID)
);
CREATE TABLE vaurkorg
			(Konto_varu INT,
			 Produkt_varu INT,
FOREIGN KEY(Konto_varu) REFERENCES konto(Person_ID),
FOREIGN KEY(Produkt_varu) REFERENCES produkt(Produkt_ID)
);
INSERT INTO produkt( Produktnamn,Produkt_ID,Img_filsökväg,Pris,Saldo,Produktbeskrivning)
VALUES('APSNUS',12,':C/något',420,69,'Smaskens')