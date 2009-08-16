#!/usr/bin/perl -w
# Um die Datenbank Funktionen zu nützen muss das DBI Modul implmentier werden
use DBI;
# User, Password und Source der Datenbank werden definiert
my $user = "root";
my $pass = "";
# Source setzt sich zusammen aus dbi:<datenbanktype>:<datenbank>:<Server auf dem die Datenbank läuft>
my $dsn = 'DBI:mysql:sis:localhost';
# Datenbankverbindung wird aufgebaut
my $dbh=DBI->connect($dsn, $user, $pass, {RaiseError => 1});
# Die File wird zeilenweise eingelesen
while(<STDIN>){
     # splitten der Zeile die eingelesen wird nach dem ";" - Zeichen
    ($muell1,$Klasse,$Lehrer,$Fach,$muell2,$Tag,$Stunde) = split(/\;/);
    $muell1="";
    $muell2="";
    
    $sql = 'Select id from Lehrer where KZ='.$Lehrer;
    #print $sql."\n";
    my $srh = $dbh->prepare($sql);
    $srh->execute();
    my $row = $srh->fetchrow_hashref();
    $Lehrer = $row->{'id'};
    
    
    $srh = $dbh->prepare('Select id from Klasse where Name='.$Klasse);
    $srh->execute();
    $row = $srh->fetchrow_hashref();
    $Klasse = $row->{'id'};
    
    $srh = $dbh->prepare('Select id from fach where Name='.$Fach);
    $srh->execute();
    $row = $srh->fetchrow_hashref();
    $Fach = $row->{'id'};
    
    # zusammenbauen des insert statementes für die MySql Datenbank
    $sql="insert into stundenplan(wtag,stunde,lehrer,fach,klasse) values (".$Tag.",".$Stunde.",".$Lehrer.",".$Fach.",".$Klasse.")";
    #print $sql.";\n";
    # insert statement wird an die Datenbank gesendet
    $dbh->do($sql);
}
$dbh->disconnect;