
	!insertmacro MUI_LANGUAGE "German"

!include "SASClient-SysFiles.nsi"
!include "SASClient-WU.nsi"
!include "SASClient-QbeClient.nsi"

Function BasePreInst
	SetAutoClose false
	SetShellVarContext all
FunctionEnd

