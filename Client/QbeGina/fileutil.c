#define UNICODE
#include <windows.h>

/// Loescht den Ordner directory und alle Unterordner und Dateien
void sys_util_deletefolder(LPCWSTR directory)
{
	WIN32_FIND_DATA FileInformation;
	HANDLE hFile;
	WCHAR fullPath[2048];

	// Alle Dateien und Ordner suchen
	wcscpy(fullPath,directory);
	wcscat(fullPath,TEXT("\\*.*"));

	hFile = FindFirstFile(fullPath,&FileInformation);
	if(hFile != INVALID_HANDLE_VALUE)
	{
		do
		{
			if((((FileInformation.cFileName[0]) == '.') &&
				((FileInformation.cFileName[1]) != '.') &&
				/*Changed NULL to '\0' produced warning on indirection levels*/
				((FileInformation.cFileName[1]) != '\0'))
			   || ((FileInformation.cFileName[0]) != '.'))
			{
				wcscpy(fullPath,directory);
				wcscat(fullPath,TEXT("\\"));
				wcscat(fullPath,FileInformation.cFileName);

				if(FileInformation.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)
				{
					// Verzeichnis gefunden, dieses mit uns selbst loeschen.
					sys_util_deletefolder((LPCTSTR)fullPath);
				}
				else
				{
					// Dateiattribute ruecksetzen
					if(SetFileAttributes(fullPath,FILE_ATTRIBUTE_NORMAL) == FALSE)
						return;

					// Datei loeschen
					if(DeleteFile(fullPath) == FALSE)
						return;

				}
			}
		} while(FindNextFile(hFile,&FileInformation) == TRUE);

		FindClose(hFile);

		if(SetFileAttributes(directory,FILE_ATTRIBUTE_NORMAL) == FALSE)
			return;

		// Das Hauptverzeichnis loeschen
		if(RemoveDirectory(directory) == FALSE)
			return;
	}
}
