#!/usr/bin/perl -w
use DBI;

# Connect to database
my $user = "root";
my $pass = "";
my $source = "dbi:mysql:sis:localhost";
my $dbh=DBI->connect($source, $user, $pass, {RaiseError => 1});
while (<STDIN>) {
    @data = split(/\;/);
    my $srh = $dbh->prepare("Select id from Lehrer where KZ=".$data[5]);
    $srh->execute();
    my $row = $srh->fetchrow_hashref();
    $Statt_Lehrer = $row->{'id'};
    
    if (!$data[6]eq""){
    $srh = $dbh->prepare("Select id from Lehrer where KZ=".$data[6]);
    $srh->execute();
    $row = $srh->fetchrow_hashref();
    $Sup_Lehrer = $row->{'id'};
    }else{
        $Sup_Lehrer=0;
    }   
    
    if ($Sup_Lehrer==0){
        $entfaellt=1;
    }else{
        $entfaellt=0;
    }
    
    print "--".$data[14];
    $srh = $dbh->prepare('Select id from Klasse where Name='.$data[14]);
    $srh->execute();
    $row = $srh->fetchrow_hashref();
    $Klasse = $row->{'id'};
    
    $srh = $dbh->prepare('Select id from fach where Name='.$data[7]);
    $srh->execute();
    $row = $srh->fetchrow_hashref();
    $Fach = $row->{'id'};
    
    $date = substr($data[1],1,4)."-".substr($data[1],5,2)."-".substr($data[1],7,2);
    $sql="insert 
    into supplierung(
        lfdnr,
        Datum,
        Klasse,
        Abteilung,
        Statt_Lehrer,
        Statt_Fach,
        Stunde,
        Sup_Lehrer,
        Sup_Fach,
        Entfaellt,
        Bemerkung,
        chUser,
        chDate) 
    values(
        NULL,
        ".$date.",
        ".$Klasse.",
        ".$Fach.",
        ".$Klasse.",
        ".$Abteilung.",
        ".$Statt_Lehrer.",
        ".$Fach.",
        ".$data[2].",
        ".$Sup_Lehrer.",
        NULL,
        ".$entfaellt.",
        NULL,
        NULL,
        NULL
        )";
    print $sql."\n";
    #$dbh->do($sql);

}
# Disconnect database connection
$dbh->disconnect;
