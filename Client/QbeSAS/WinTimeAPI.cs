using System;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	public class WinTimeAPI
	{
#if !UNIX
		[StructLayout(LayoutKind.Sequential, Pack=1)]
			internal struct TokPriv1Luid
		{
			public int  Count;
			public long  Luid;
			public int  Attr;
		}
		
		[DllImport("kernel32.dll", SetLastError=true)] 
		internal static extern UInt32 SetSystemTime(ref SYSTEMTIME lpSystemTime);

		[DllImport("kernel32.dll", ExactSpelling=true) ]
		internal static extern IntPtr GetCurrentProcess();

		[DllImport("advapi32.dll", ExactSpelling=true, SetLastError=true) ]
		internal static extern bool OpenProcessToken( IntPtr h, int acc, ref IntPtr phtok );

		[DllImport("advapi32.dll", SetLastError=true) ]
		internal static extern bool LookupPrivilegeValue( string host, string name, ref long pluid );

		[DllImport("advapi32.dll", ExactSpelling=true, SetLastError=true) ]
		internal static extern bool AdjustTokenPrivileges( IntPtr htok, bool disall, ref TokPriv1Luid newst, int len, IntPtr prev, IntPtr relen );

		internal const int SE_PRIVILEGE_ENABLED = 0x00000002;
		internal const string SE_SYSTEMTIME_NAME = "SeSystemtimePrivilege";
		internal const int  TOKEN_QUERY    = 0x00000008;
		internal const int  TOKEN_ADJUST_PRIVILEGES = 0x00000020;

		internal struct SYSTEMTIME
		{
			public UInt16 wYear;
			public UInt16 wMonth;
			public UInt16 wDayOfWeek;
			public UInt16 wDay;
			public UInt16 wHour;
			public UInt16 wMinute;
			public UInt16 wSecond;
			public UInt16 wMilliseconds;
		}

		public static bool SetTime(System.DateTime NewDateTime)
		{
			SYSTEMTIME sysTime = new SYSTEMTIME();

			try 
			{
				sysTime.wDayOfWeek = 0;	// ignored by setsystime

				sysTime.wDay = (UInt16)NewDateTime.Day;
				sysTime.wMonth = (UInt16)NewDateTime.Month;
				sysTime.wYear = (UInt16)NewDateTime.Year;

				sysTime.wHour = (UInt16)NewDateTime.Hour;
				sysTime.wMinute = (UInt16)NewDateTime.Minute;
				sysTime.wSecond = (UInt16)NewDateTime.Second;

				sysTime.wMilliseconds = (UInt16)NewDateTime.Millisecond;

				bool   ok;
				TokPriv1Luid tp;
				IntPtr hproc = GetCurrentProcess();
				IntPtr htok = IntPtr.Zero;
				ok = OpenProcessToken( hproc, TOKEN_ADJUST_PRIVILEGES | TOKEN_QUERY, ref htok );
				tp.Count = 1;
				tp.Luid = 0;
				tp.Attr = SE_PRIVILEGE_ENABLED;
				ok = LookupPrivilegeValue( null, SE_SYSTEMTIME_NAME, ref tp.Luid );
				ok = AdjustTokenPrivileges( htok, false, ref tp, 0, IntPtr.Zero, IntPtr.Zero );

				UInt32 ui = SetSystemTime(ref sysTime);
				if (ui == 0)
					return false;
			} 
			catch (Exception ex) 
			{	ex=ex;
				return false;
			}
			return true;
		}
#else
		// stub.
		public static bool SetTime(System.DateTime NewDateTime)
		{
			return false;
		}
#endif
	}
}
