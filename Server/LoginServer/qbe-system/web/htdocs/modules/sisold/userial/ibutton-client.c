// Includieren der Headerfiles
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <netdb.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <sys/io.h>
#include "ownet.h"

// PORT Nummer auf der der I-Button client läuft
#define PORT 13500

// SPORT ist der Port auf dem der Seriale Adapter angeschlossen ist
#define SPORT "/dev/tts/0"

// Variable in der die I-Button Serial gespeichert wird
uchar SNum[9];

///////////////////////////////////////////////////////////////////////////////
// int msg(char msg, int s)
// Sendet die Nachricht an den offenen Socket
// Parameter:
//           char msg = message die gesendet wird
//	     int s    = handler der socket verbindung
// Rückgabewert:
//	     -1 : wenn ein fehler auftritt
//           0> : die Anzahl an zeichen die gesendet werden

int msg(char msg[], int s)
{
    int b;
    b = send(s,msg,strlen(msg),0);
    return(b);
}
///////////////////////////////////////////////////////////////////////////////
// int get_ibutton(void)
// hohlt die Ibutton Serialnummber vom Ibutton und schreibt sie in die Variable SNum
// Parameter:
//           keine
// Rückgabe:
//           0 : ein Fehler ist aufgetreten
//           1 : kein Fehler aufgetreten

int get_ibutton(void)
{
    int rslt, cnt;
    int portnum=0;
	
    if (!owAcquire(portnum,SPORT))
    {
	return(0);
    }
    
    rslt = owFirst(portnum, TRUE, FALSE);
    if (rslt) {
	owSerialNum(portnum,&SNum[0],TRUE);
    }else{
	return(0);
    }
    owRelease(portnum);
    return(1);
}


int main(){
    int sock, msgsock, length, rval, ex, ibu, i,x;
    struct hostent *host;
    struct sockaddr_in server;
    char buffer[1024], test[1024], test2[3];
        
    host = gethostbyname(HOST);
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
	printf("Fehler beim oeffnen des Sockets\n"); 
	exit(1);
    }
    server.sin_family = AF_INET;
    server.sin_addr.s_addr = INADDR_ANY;
    server.sin_port = htons(PORT);
    if(bind(sock, (struct sockaddr *)&server, sizeof(server)) < 0) {
	printf("Fehler beim Socket binden\n");
	exit(1);
    }
    for(;;){
    	// höhren am Socket, mit 3 connections
        listen (sock, 3);
        // eine Connction akzeptiern
	msgsock = accept(sock, NULL, NULL);
	// wenn msdgsock -1 zurückliefert ist ein Fehler aufgetreten
	if (msgsock == -1) {
	    printf("Fehler beim Listen am Socket\n");
	} else {
	    memset(buffer,0,sizeof(buffer));
	    rval = read(msgsock, buffer, sizeof(buffer));
	    
	    if (rval < 0)
	    {
		printf("Fehler beim Lesen\n"); 
		exit(1);
	    }
	    
	    if (rval == 0)
	    {
		printf("Verbindung abgebaut\n");
	    }else{
		    
	    strcpy(test,"Serial");
	    if (strncmp(buffer,test,strlen(test))==0){
		char name[16]="";
		char temp_num[22];
		ibu = get_ibutton();
		if (ibu) {
			for (x=7;x>=0;x--){
				sprintf(temp_num,"%02hhX",SNum[x]);
				strcat(name,temp_num);
			}
		}else{
			strcpy(name,"NC");
		}
		msg(name,msgsock);
                close(msgsock);
		}
	    }
	    
	    strcpy(test,"Close");
	    if (strncmp(buffer,test,strlen(test))==0){
		close(msgsock);
                exit(0);
	    }
	}
    }
}
