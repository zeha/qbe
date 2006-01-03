//
// $Id$
// (C) Copyright 2004 Christian Hofstaedtler
//

#include <windows.h>
#include <stdio.h>
#include <wincrypt.h>
#include <fcntl.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <io.h>

#define MY_ENCODING_TYPE  (PKCS_7_ASN_ENCODING | X509_ASN_ENCODING)

void HandleTheError(const char *s);


int WinMain( HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow)
{
	HCERTSTORE      hSystemStore;

	BYTE		certData[2002];
	WORD		certDataLen;
	int		f;

	char* filename = "cert.der";
	if (lpCmdLine != NULL)
		if (strlen(lpCmdLine) > 0)
			filename = lpCmdLine;
	
	if ( (f = _open(filename,_O_RDONLY|_O_BINARY,_S_IREAD)) == -1)
		HandleTheError("Certificate file could not be read!");

	certDataLen = read(f,certData,2000);
	_close(f);


	// get cert store
	if ( (hSystemStore = CertOpenStore(
		CERT_STORE_PROV_SYSTEM,
		0,
		0,
		CERT_SYSTEM_STORE_LOCAL_MACHINE,
		L"ROOT"
		)) == 0	)
	{
		HandleTheError("The first system store did not open.");
	}


	// add cert
	if( (CertAddEncodedCertificateToStore(
	     hSystemStore,
	     MY_ENCODING_TYPE,
	     certData,
	     certDataLen,
	     CERT_STORE_ADD_USE_EXISTING,
	     NULL)) == 0 )
	{
	     HandleTheError("The certificate could not be added.");
	}

	CertCloseStore(hSystemStore,0);
}

void HandleTheError(const char *s)
{
	const char* szMessage = "The certificate was not added for this reason:\n";
	const char* szErrNumMessage = "\n\nError Number: 0x";
	char* szFullErrString = (char*)malloc(strlen(s) + strlen(szErrNumMessage) + 10);
	sprintf(szFullErrString,"%s%s%s%08x",szMessage,s,szErrNumMessage,GetLastError());
	MessageBox(0,szFullErrString,"Qbe Certificate Utility",MB_OK|MB_ICONSTOP);
	exit(1);		
}

