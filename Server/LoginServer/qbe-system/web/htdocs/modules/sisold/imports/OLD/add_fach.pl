#!/usr/bin/perl -w

# Um die Datenbank Funktionen zu n�tzen muss das DBI Modul implmentier werden
use DBI;

# User, Password und Source der Datenbank werden definiert
my $user = "root";
my $pass = "";
# Source setzt sich zusammen aus dbi:<datenbanktype>:<datenbank>:<Server auf dem die Datenbank l�uft>
my $source = "dbi:mysql:sis:localhost";
# Datenbankverbindung wird aufgebaut
my $dbh=DBI->connect($source, $user, $pass, {RaiseError => 1});
# Die File wird zeilenweise eingelesen
while (<STDIN>) {
    # splitten der Zeile die eingelesen wird nach dem ";" - Zeichen
    ($name,$bes) = split(/\;/);
    # zusammenbauen des insert statementes f�r die MySql Datenbank
    $sql='insert into fach(id,Name,Beschreibung) values (NULL,'.$name.','.$bes.')';
    # insert statement wird an die Datenbank gesendet
    $dbh->do($sql);
    #print $sql."\n";  debugging only
}

# Datenbankverbindung wird geschlossen

$dbh->disconnect;
