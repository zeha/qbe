;
; $Id: SASClient.nsi 126 2004-06-02 13:56:08Z ch $
; (c) Copyright 2002-2004 Christian Hofstaedtler
;
; vi:noai:ts=4
;
SetCompressor zlib

!ifndef QBEOUTDIR
!define QBEOUTDIR ..\BIN\RETAIL\I386_32_2000
!endif

!include "prefix.nsh"
!include "UpgradeDLL.nsh"

	InstallDir "$SYSDIR\Qbe"
	Name "${PRODUCT_PRODUCT}"

	SetDateSave on
	SetCompress auto
	SetDatablockOptimize on

