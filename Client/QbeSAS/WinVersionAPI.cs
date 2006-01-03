using System;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	/// Abstraktion um das Win32 GetVersionEx() API
	public class WinVersionAPI
	{
		[StructLayout(LayoutKind.Sequential)]
		private class OSVERSIONINFO 
		{
			public int dwOSVersionInfoSize = 0;
			public int dwMajorVersion = 0;
			public int dwMinorVersion = 0;
			public int dwBuildNumber = 0;
			public int dwPlatformId = 0;
			[MarshalAs (System.Runtime.InteropServices.UnmanagedType.ByValTStr, SizeConst=128)]
			public string szCSDVersion = "";
		};

		[DllImport("kernel32")]
        private extern static short GetVersionExA([In, Out] [MarshalAs(System.Runtime.InteropServices.UnmanagedType.LPStruct)] OSVERSIONINFO lpVersionInformation );

		/// Service Pack Nummer abrufen
		public static int getServicePack()
		{
			OSVERSIONINFO osinfo = new OSVERSIONINFO();
			short retvalue;

			osinfo.dwOSVersionInfoSize = 148;
			retvalue = GetVersionExA(osinfo);
			if (osinfo.szCSDVersion.Length == 0)
				return 0;
			else
				return int.Parse(osinfo.szCSDVersion.Substring(13, 1));		
		}
	
	}
}
