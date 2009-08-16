Load Data Local
infile '/srv/www/htdocs/Import Scripts/abteilungen_aktuell.csv'
replace
into table abteilung
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/faecher_aktuell.csv'
replace
into table fach
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/kategorie_aktuell.csv'
replace
into table kat
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/klassen_aktuell.csv'
replace
into table klasse
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/lehrer_aktuell.csv'
replace
into table lehrer
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/stunden_aktuell.csv'
replace
into table stunde
fields
terminated by ';'
optionally enclosed by '"';

Load Data Local
infile '/srv/www/htdocs/Import Scripts/tage_aktuell.csv'
replace
into table tage
fields
terminated by ';'
optionally enclosed by '"';
