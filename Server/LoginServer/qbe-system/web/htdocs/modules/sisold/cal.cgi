#!/usr/bin/perl -w
use Time::localtime;
use Time::Local;
use CGI;
my $cgi = new CGI;
@Week = ("Mo","Di","Mi","Do","Fr","Sa");
$year = localtime->year();
$day = localtime->mday()+($cgi->param('offset')*7);
$dayende=$day+7;
$month = localtime->mon()+1;
while ($day>31){       
    if (($month==2) and ($year%4)==0 and $day>29) {
        $day -= 29;
        ++$month;
    }
    if (($month==2) and ($year%4)!=0 and $day>28) {
        $day -= 28;
        ++$month;
    }
    if (($month==4 or $month==6 or $month==9 or $month==11) and $day>30) {
        $day -= 30;
        ++$month;
    }
    if (($month==1 or $month==3 or $month==5 or $month==7 or $month==8 or $month==10 or $month==12) and $day>31) { 
        $day -= 31;
        ++$month;
        if ($month>12){
            $month=1;
            ++$year;
        }
    }
}
$time = timelocal(0,0,0,$day,$month-1,$year);
$year = localtime($time)->year()+1900;
$day = localtime($time)->mday();
$month = localtime($time)->mon()+1;
$wd = localtime($time)->wday()-1;
print $cgi->header;
print $cgi->start_html("Kalender");
print '<table border=0>'."\n";
print '<h2>'.$day.'.'.$month.' - '.$dayende.'.'.$month.'</h2>';
for ($i=0;$i<6;++$i){        
    $day=localtime($time)->mday()-($wd-$i);
    print '<tr><td>';
    print $Week[$i].'<br>'.$day.'.'.$month.'.'.$year;
    print '</td><td>&nbsp;</td></tr>'."\n";
    if (($month==2) and ($year%4)==0 and $day>29) {
        $day -= 29;
        ++$month;
    }
    if (($month==2) and ($year%4)!=0 and $day>28) {
        $day -= 28;
        ++$month;
    }
    if (($month==4 or $month==6 or $month==9 or $month==11) and $day>=30) {
        $day -= 30;
        ++$month;
    }
    if (($month==1 or $month==3 or $month==5 or $month==7 or $month==8 or $month==10 or $month==12) and $day>=31) { 
        $day -= 31;
        ++$month;
        if ($month>12){
            $month=1;
            ++$year;
        }
    }    
}
print '</table></body></html>';