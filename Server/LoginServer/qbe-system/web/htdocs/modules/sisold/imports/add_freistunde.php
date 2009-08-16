<html>
<head>
<title>Freistunden-Generierung</title>
</head>
<body>
Beginne Freistundengenerierung...
<?php
$eintraege = 0;
set_time_limit(10000);
include("db.inc");
$db_query = 'select id from lehrer';
$db_query_res = @mysql_db_query($db_name,$db_query);
if (@mysql_affected_rows()!=0) {
    while($row = @mysql_fetch_array($db_query_res)) {
        for ($x=1;$x<=6;$x++) {
            for ($y=1;$y<=17;$y++) {
                $db_query2 = '
                select
                    *
                from
                    stundenplan
                where
                    Lehrer='.$row['id'].'
                and
                    Stunde='.$y.'
                and
                    WTag='.$x;
                $db_query_res2 = mysql_db_query($db_name,$db_query2);
                if (mysql_affected_rows()==0) {
                    $db_query3 = '
                    insert into
                        stundenplan
                    values
                    ('.$x.',
                    '.$y.',
                    NULL,
                    NULL,
                    '.$row['id'].')';
                    $db_query_res2 = mysql_db_query($db_name,$db_query3);

                    $eintraege++;
                }
            }
        }
    }
}
?>
<br>
<br>
Es wurden neue Eintr&auml;ge hinzugef&uuml;gt.

</body>
</html>