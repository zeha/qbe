using System;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	/// WinServiceAPI stellt Routinen zur Verwaltung des QbeSvc unter Windows zur Verfuegung.
	/// In der aktuellen Version ist nur RemoveService() vorhanden.
	public class WinServiceAPI
	{
#if !UNIX
		[StructLayout(LayoutKind.Sequential)]
		internal class SERVICE_STATUS 
		{
			public int dwServiceType = 0;
			public int dwCurrentState = 0;
			public int dwControlsAccepted = 0;
			public int dwWin32ExitCode = 0;
			public int dwServiceSpecificExitCode = 0;
			public int dwCheckPoint = 0;
			public int dwWaitHint = 0;
		};

		[DllImport("Advapi32", SetLastError=true)]
		internal static extern int OpenSCManager( string MachineName, string DatabaseName, int dwDesiredAccess);
		[DllImport("Advapi32")]
		internal static extern int OpenService( int hSCManager, string lpServiceName, int dwDesiredAccess);
		[DllImport("Advapi32")]
		internal static extern int ControlService( int hService, int action, [Out, MarshalAs(System.Runtime.InteropServices.UnmanagedType.LPStruct)] SERVICE_STATUS status);
		[DllImport("Advapi32")]
		internal static extern int QueryServiceStatus( int hService, [Out, MarshalAs(System.Runtime.InteropServices.UnmanagedType.LPStruct)] SERVICE_STATUS status);
		[DllImport("Advapi32", SetLastError=true)]
		internal static extern int CreateService(int hSCM, string lpSvcName,string lpDisplayName,
			int dwDesiredAccess,int dwServiceType,int dwStartType,int dwErrorControl,string lpPathName,
			string lpLoadOrderGroup,int lpdwTagId,string lpDependencies,string lpServiceStartName,string lpPassword);
		[DllImport("Advapi32")]
		internal static extern int DeleteService( int hService );
		[DllImport("Advapi32")]
		internal static extern int CloseServiceHandle( int hService );

		[StructLayout(LayoutKind.Sequential, Pack=1)]
		internal struct TokPriv1Luid
		{
			public int  Count;
			public long  Luid;
			public int  Attr;
		}

		[DllImport("kernel32.dll", ExactSpelling=true) ]
		internal static extern IntPtr GetCurrentProcess();

		[DllImport("advapi32.dll", ExactSpelling=true, SetLastError=true) ]
		internal static extern bool OpenProcessToken( IntPtr h, int acc, ref IntPtr phtok );

		[DllImport("advapi32.dll", SetLastError=true) ]
		internal static extern bool LookupPrivilegeValue( string host, string name, ref long pluid );

		[DllImport("advapi32.dll", ExactSpelling=true, SetLastError=true) ]
		internal static extern bool AdjustTokenPrivileges( IntPtr htok, bool disall,
			ref TokPriv1Luid newst, int len, IntPtr prev, IntPtr relen );

		[DllImport("user32.dll", ExactSpelling=true, SetLastError=true) ]
		internal static extern bool ExitWindowsEx( int flg, int rea );

		internal const int  SE_PRIVILEGE_ENABLED = 0x00000002;
		internal const int  TOKEN_QUERY    = 0x00000008;
		internal const int  TOKEN_ADJUST_PRIVILEGES = 0x00000020;
		internal const string SE_SHUTDOWN_NAME  = "SeShutdownPrivilege";
		internal const int  EWX_LOGOFF    = 0x00000000;
		internal const int  EWX_SHUTDOWN   = 0x00000001;
		internal const int  EWX_REBOOT    = 0x00000002;
		internal const int  EWX_FORCE    = 0x00000004;
		internal const int  EWX_POWEROFF   = 0x00000008;
		internal const int  EWX_FORCEIFHUNG   = 0x00000010;

		internal const int SERVICE_WIN32_OWN_PROCESS = 0x00000010;
		internal const int SERVICE_AUTO_START   = 0x00000002;
		internal const int SERVICE_DEMAND_START = 0x00000003;
		internal const int SERVICE_ERROR_NORMAL = 0x00000001;

		internal const int STANDARD_RIGHTS_REQUIRED       =0xF0000;
		internal const int SERVICE_QUERY_CONFIG           =0x0001;
		internal const int SERVICE_CHANGE_CONFIG          =0x0002;
		internal const int SERVICE_QUERY_STATUS           =0x0004;
		internal const int SERVICE_ENUMERATE_DEPENDENTS   =0x0008;
		internal const int SERVICE_START                  =0x0010;
		internal const int SERVICE_STOP                   =0x0020;
		internal const int SERVICE_PAUSE_CONTINUE         =0x0040;
		internal const int SERVICE_INTERROGATE            =0x0080;
		internal const int SERVICE_USER_DEFINED_CONTROL   =0x0100;

		internal const int SERVICE_ALL_ACCESS             =  (STANDARD_RIGHTS_REQUIRED     |
			SERVICE_QUERY_CONFIG         |
			SERVICE_CHANGE_CONFIG        |
			SERVICE_QUERY_STATUS         |
			SERVICE_ENUMERATE_DEPENDENTS |
			SERVICE_START                |
			SERVICE_STOP                 |
			SERVICE_PAUSE_CONTINUE       |
			SERVICE_INTERROGATE          |
			SERVICE_USER_DEFINED_CONTROL);


		public static bool ExitWindows( int Flags )
		{
			bool   ok;
			TokPriv1Luid tp;
			IntPtr hproc = GetCurrentProcess();
			IntPtr htok = IntPtr.Zero;
			ok = OpenProcessToken( hproc, TOKEN_ADJUST_PRIVILEGES | TOKEN_QUERY, ref htok );
			tp.Count = 1;
			tp.Luid = 0;
			tp.Attr = SE_PRIVILEGE_ENABLED;
			ok = LookupPrivilegeValue( null, SE_SHUTDOWN_NAME, ref tp.Luid );
			ok = AdjustTokenPrivileges( htok, false, ref tp, 0, IntPtr.Zero, IntPtr.Zero );
			ok = ExitWindowsEx( Flags, 0 );
			return true;
		}

		public static bool RestartWindows()
		{
			return ExitWindows(EWX_REBOOT + EWX_FORCE);
		}
		public static bool ShutdownWindows()
		{
			return ExitWindows(EWX_SHUTDOWN + EWX_FORCE);
		}

		public static bool InstallService(string svcPath)
		{
			const int accessManagerCreate = 0x0002;

			int schService;
			int schSCManager;
			schSCManager = OpenSCManager(null,null,accessManagerCreate);

			if (schSCManager != 0)
			{
				schService = CreateService(schSCManager,"QbeSVC","Qbe Network Authentication",
					SERVICE_ALL_ACCESS,SERVICE_WIN32_OWN_PROCESS,SERVICE_AUTO_START,SERVICE_ERROR_NORMAL,
					svcPath,
					null,0,null,null,null);
				
				if (schService == 0)
				{
					System.ComponentModel.Win32Exception w32ex = new System.ComponentModel.Win32Exception(Marshal.GetLastWin32Error());
					CloseServiceHandle(schService);
					throw w32ex;
				}
				else
				{
					CloseServiceHandle(schSCManager);
					return true;
				}
			}
			else
			{
				throw new System.ComponentModel.Win32Exception(Marshal.GetLastWin32Error());
			}
		}

		/// Stoppt und entfernt den QbeSvc.
		/// Dazu sind Administratorrechte erforderlich.
		public static bool RemoveService()
		{
			int schService;
			int schSCManager;
			SERVICE_STATUS ssStatus = new SERVICE_STATUS();

			const int accessManagerConnect = 0x0001;
			const int accessServiceDelete = 0x00010000;
			const int accessServiceStop = 0x0020;
			const int accessServiceQueryStatus = 0x0004;

			const int controlServiceStop = 0x01;
			const int serviceStopPending = 0x00000003;

			// ServiceControl Manager oeffnen
			schSCManager = OpenSCManager( null, null, accessManagerConnect );

			if ( schSCManager != 0 )
			{
				// Service oeffnen
				schService = OpenService(schSCManager, "qbesvc", accessServiceDelete | accessServiceStop | accessServiceQueryStatus);

				if (schService != 0 )
				{
					// try to stop the service
					if ( ControlService( schService, controlServiceStop, ssStatus ) != 0 )
					{
						System.Threading.Thread.Sleep( 1000 );
						while ( QueryServiceStatus( schService, ssStatus ) != 0 )
						{
							if ( ssStatus.dwCurrentState == serviceStopPending )
								System.Threading.Thread.Sleep( 1000 );
                           	else
								break;
						}
					}

					DeleteService(schService);
					CloseServiceHandle(schService);
                }
				CloseServiceHandle(schSCManager);
				return true;
			} 
			else 
				return false;

		}
#else
		// stubs for unix
		public static bool RemoveService()
		{
			return false;
		}
		public static bool RestartWindows()
		{
			return false;
		}
		public static bool ShutdownWindows()
		{
			return false;
		}
#endif
	}
}
