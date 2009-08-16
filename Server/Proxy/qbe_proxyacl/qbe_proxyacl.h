/* qbe_proxyacl.h : (C) Copyright 2002 Christian Hofstaedtler
   $Id: qbe_proxyacl.h,v 1.4 2002/11/10 15:15:28 ch Exp $
 */

#include <string.h>
#include <stdio.h>
#include <malloc.h>

#define TABLESIZE 2000
#define TABLEPATH "/import/homes/.status/proxyacl.bin"

typedef struct aclEntry {
        char aclUsername[25];
        char aclIp[16];
        char aclMac[19];                /* 00-03-47-B9-21-F4 */
} aclEntry;

/* eof */
