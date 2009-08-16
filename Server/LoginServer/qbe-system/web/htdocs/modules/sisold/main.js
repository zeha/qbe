function hide_all(){
for (var x=0;x<parent.frames[1].document.getElementsByTagName("div").length;x++){
	parent.frames[1].document.getElementsByTagName("div")[x].style.visibility="hidden";
}
}

function jump_daten(url) {
    parent.frames[1].document.location=url;   
}

function jump_edvo(url) {
	parent.frames[1].document.location=url;
}

function hoover_in(objekt,col){
	objekt.style.color=col;
}

function hoover_out(objekt,col){
	objekt.style.color=col;
}

function jump(url){
	document.location.href=url;
}

function menu_vis(objekt){
for (var x=0;x<parent.frames[1].document.getElementsByTagName("div").length;x++){
	parent.frames[1].document.getElementsByTagName("div")[x].style.visibility="hidden";
}
	parent.frames[1].document.getElementById(objekt).style.top=parent.frames[1].pageYOffset;
	parent.frames[1].document.getElementById(objekt).style.visibility="visible";
}

function menu_hid(objekt){
parent.daten.document.getElementById(objekt).style.visibility="hidden";
}

function test(object){
    if (object.checked==true) {
        document.getElementById("sup_tr").style.visibility="hidden";
    }else{
        document.getElementById("sup_tr").style.visibility="visible";
    }
}

function formatdate(datum,objekt,format){
    var Wochentag = new Array("SO","MO","DI","MI","DO","FR","SA");
    var test = datum.split(' ');
    if (format==1){        
        if (test.length==1)
        {
        test = test[0].split('.');        
        var dat = new Date(test[2],test[1]-1,test[0]);
        month = dat.getMonth()+1;
        var output = Wochentag[dat.getDay()]+' '+dat.getDate()+'.'+month+'.'+dat.getFullYear();
        document.getElementById(objekt).value = output;
        }
    }else{
        if (test.length!=1){
            test = test[1].split('.');
            var dat = new Date(test[2],test[1]-1,test[0]);
            month = dat.getMonth()+1;
            var output = dat.getDate()+'.'+month+'.'+dat.getFullYear();
            document.getElementById(objekt).value = output;
        }
    }
}

function formateddate(objekt,format){
    var dat = new Date();
    var Wochentag = new Array("SO","MO","DI","MI","DO","FR","SA");
    if (format==1) {
        var dayinweek = dat.getDay();
        var month = dat.getMonth()+1;
        var output = Wochentag[dayinweek]+' '+dat.getDate()+'.'+month+'.'+dat.getFullYear();
        document.getElementById(objekt).value = output;
    }else{
        var output = dat.getDate()+'.'+dat.getMonth()+'.'+dat.getFullYear();
        formateddate = output
    };
}
    
function reload() {
    parent.frames[1].location.reload();
}

function send_check(id,user) {
    window.open("che_sup.php?id="+id+"&user="+user,"Fenster1","width=1,height=1,left=0,top=0");
}

function send_check_termin(id) {
    window.open("che_term.php?id="+id,"Fenster1","width=1,height=1,left=0,top=0");
}

function jump_status(url) {
    parent.frames[2].document.location=url;   
}

function cursor_hand(object) {
        object.style.cursor = "pointer";
}