using System;

namespace QbeSAS
{
	/// <summary>
	/// Funktionen fuer QbePFC/QbeNDI
	/// 
	/// </summary>
	public class PFC
	{
		/// Late-binding entry point for QbePFC.exe
		/// DO NOT RENAME
		public static bool pfc_setup(bool removeService)
		{
			QbeSAS.PFC pfc = new QbeSAS.PFC();
			pfc.execKillService(removeService);
			pfc.execKillApps();
			pfc.cleanup();
			return true;
		}
		
		/// 1 Sekunde warten
		public bool cleanup()
		{
			System.Threading.Thread.Sleep(1000);
			return true;
		}

		/// QbeSvc stoppen, entfernen und eventuell abschiessen
		/// Es werden alle Moeglichkeiten versucht um den QbeSvc zu entfernen.
		public bool execKillService(bool remove)
		{
			System.Diagnostics.ProcessStartInfo pi;
			if (remove)
				QbeSAS.WinServiceAPI.RemoveService();

			pi = new System.Diagnostics.ProcessStartInfo();
			pi.WindowStyle = System.Diagnostics.ProcessWindowStyle.Minimized;

			try 
			{
				System.ServiceProcess.ServiceController svcCtrl;
				svcCtrl = new System.ServiceProcess.ServiceController("qbesvc");
				svcCtrl.Refresh();
				svcCtrl.Stop();
				svcCtrl.Refresh();

				if (svcCtrl.Status != System.ServiceProcess.ServiceControllerStatus.Stopped)
				{
					pi.FileName = "net.exe";
					pi.Arguments = "stop qbesvc";
					System.Diagnostics.Process.Start(pi);
				}
			} 
			catch (Exception e) { e=e; }

			return true;
		}

		/// Beendet alle moeglichen Applikationen die die Installation beeintraechtigen koennen
		/// Das sind z.B. der qbesvc, qbetray, Internet Explorer und alle alten iLogin/Qbe Client Applikationen
		public bool execKillApps()
		{
			String ProcName;

			foreach (System.Diagnostics.Process Proc in System.Diagnostics.Process.GetProcesses())
			{
				try
				{
				ProcName = Proc.ProcessName.ToLower();
				if (
					(ProcName == "qbesvc") ||	// q service
					(ProcName == "qbetray") ||	// q tray icon
					(ProcName == "mshta") ||	// microsoft hypertext applications
					(ProcName == "ilogin") ||	// OOOLD ilogin executable
					(ProcName == "iloginlite") ||	// OOOLD ilogin executable
					(ProcName == "qbestart") ||	// old ilogin stuff
					(ProcName == "qbelauncher") ||	// old ilogin stuff
					(ProcName == "iliveup") ||	// OOOLD ilogin update saug0r
					(ProcName == "rpcsvc") ||	// OOOLD ilogin service
					(ProcName == "iexplore") ||	// Microsoft Internet Explorer
					(ProcName == "bdmcon")		// bitdefender console
				   )
					Proc.Kill();
				} 
				catch (Exception e) { e=e; }

			}

			foreach (System.Diagnostics.Process Proc in System.Diagnostics.Process.GetProcesses())
			{
				try
				{
				ProcName = Proc.ProcessName.ToLower();
				if (ProcName == "qbesvc") 
				{
					Proc.Kill();
					while (!Proc.HasExited)
						Proc.WaitForExit(5000);
				}
				} 
				catch (Exception e) { e=e; }
			}
			return true;
		}

		/// Abfrage der Service Pack Version sowie Download (mit ServicePack.exe) des aktuellen Service Packs
		/// Es wird nur Windows 2000 (5.0) und XP (5.1) abgefragt. Unter NT4.0 laeufts eh nicht, und Win2003 sollte
		/// ohne weiteres funktionieren.
		public bool execCheckServicePack()
		{
			try
			{
			System.Diagnostics.Process procSPack;
			System.OperatingSystem os;

			os = Environment.OSVersion;

			if (os.Version.Major == 5)
			{
				int spack = QbeSAS.WinVersionAPI.getServicePack();

				if (os.Version.Minor == 0 && spack < 3)
				{
					procSPack = System.Diagnostics.Process.Start("ServicePack.exe", "os=5.0");
					while (!procSPack.HasExited)
						System.Threading.Thread.Sleep(1000);
				}
				if (os.Version.Minor == 1 && spack < 1)
				{
					procSPack = System.Diagnostics.Process.Start("ServicePack.exe", "os=5.1");
					while (!procSPack.HasExited)
						System.Threading.Thread.Sleep(1000);
				}
			}
			} 
			catch (Exception e) { e=e; }
			return true;
		}
	}
}
