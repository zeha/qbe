/* writeacl.c : (C) Copyright 2002 Christian Hofstaedtler
   $Id: qbe_writeacl.c,v 1.3 2002/11/19 17:02:40 ch Exp $
 */

#include "qbe_proxyacl.h"

aclEntry ACLTable[TABLESIZE+1];

int main(void)
{
 FILE* fd; int entry;
 char szTemp[1024];
 printf("Qbe systems: qbe_proxyacl/writeacl 0.01\n");
 printf("(C) Copyright 2002 Christian Hofstaedtler\n");

 fd = fopen(TABLEPATH,"wb");
 if (fd == NULL)
 {
    printf("ERR");
    perror("fopen");
    return -1;
 }
 
 entry = 0;
 while ( fgets(szTemp,1024,stdin) != NULL )
 {
 	if (strlen(szTemp) < 4)
 		break;
 		
	sscanf(szTemp,"%s %s %s", 
 		ACLTable[entry].aclUsername,
 		ACLTable[entry].aclIp,
 		ACLTable[entry].aclMac);
 	if (++entry > TABLESIZE)
 		break;
 }
/*  for (entry=0;entry<TABLESIZE;entry++)
  {
     sprintf(ACLTable[entry].aclUsername,"u%05d",entry);
  }
*/
  
  
 fwrite( &ACLTable , sizeof(aclEntry), TABLESIZE, fd);
 fclose(fd);
 return 0;
}                                                                                                                                                                                                                                                                

