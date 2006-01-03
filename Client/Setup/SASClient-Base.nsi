;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;


	BrandingText "Qbe Installation ${PRODUCT_VERSION}"
	XPStyle on
	ShowInstDetails hide
	ShowUninstDetails hide

	SetOverwrite on

	; language warnings !?!?!!?!?!?
;	VIAddVersionKey ProductName "Qbe SAS"
;	VIAddVersionKey FileDescription "${PRODUCT_PRODUCT} Software Setup"
;	VIAddVersionKey FileVersion "${PRODUCT_VERSION}"
;	VIAddVersionKey ProductVersion "${PRODUCT_VERSION}"
;	VIAddVersionKey CompanyName "Qbe Austria -- http://qbe.ch/"
;	VIAddVersionKey InternalName "Qbe Software Setup"
;	VIAddVersionKey OriginalFilename "QbeInstall.exe"
;	VIAddVersionKey LegalCopyright "Copyright (C) 2001-2004 Christian Hofstaedtler"
;	VIProductVersion "2.0.0.0"


	!define MUI_ICON "..\Common\Q.ico"
	!define MUI_UNICON "..\Common\Q.ico"

	!define MUI_WELCOMEPAGE_TITLE "${PRODUCT_PRODUCT} ${PRODUCT_VERSION}"
	!define MUI_FINISHPAGE_NOAUTOCLOSE

	!define MUI_ABORTWARNING
	!define MUI_PROGRESSBAR smooth
	!define MUI_FINISHPAGE_RUN "$INSTDIR\QbeTray.exe"

	!define MUI_WELCOMEFINISHPAGE_BITMAP "nsis\special.bmp"

;--------------------------------
;

