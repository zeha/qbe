#pragma once

#define		QBE_LDAP_SERVER		TEXT("qbe-auth")
#define		QBE_LDAP_PORT		389
#define		QBE_LDAP_BASE		TEXT("o=htlwrn,c=at")
#define		QBE_LDAP_SEARCH		TEXT("uid=%s")

#define		QBE_LDAP_HOSTSEARCH	TEXT("uid=%s$")


/// Enthaelt die Konfiguration fuer die lokale Arbeitsstation ("HostPolicy")
struct QbeSAS_HostPolicy 
{
	/// Soll ein Lokaler Benutzer bei erfolgreicher Server-Authentifizierung erstellt werden
	int enableDynamicUser;
	/// Gruppenname fuer den lokalen Benutzer
	LPWSTR DynamicUserGroup;
	/// Pfad und Parameter zum Login-Skript
	LPWSTR LoginScript;
	/// Laufwerksbuchstabe fuer das Benutzerverzeichnis (z.B. Q)
	LPWSTR HomeDrive;
	/// Pfad zum Benutzerverzeichnis
	LPWSTR HomeDriveDir;
};

extern int qbe_ldap_checkuser(LPWSTR username, LPWSTR password);
extern int qbe_ldap_getpolicy( LPWSTR szWorkstationName, struct QbeSAS_HostPolicy *policy );


// *eof*

