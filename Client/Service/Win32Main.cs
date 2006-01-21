using System;
using System.Diagnostics;
using System.ServiceProcess;
using System.Runtime.InteropServices;
[assembly:ComVisible(false)]

namespace QbeService
{
	/// Stellt die Umgebung fuer den Win32-Client zur Verfuegung.
	class Win32Main
	{
		/// Einsprungsfunktion fuer den Win32-Client
		/// -start wird vom SCM uebergeben
		/// -debug agiert wie ein XPlat Client start
		/// -V zeigt die Versionsnummer
		[STAThread]
		static void Main(string[] args)
		{
			// Version ausgeben
			Console.WriteLine("Qbe SAS Client (Win32) " + QbeSAS.QbeClientVersion.ClientVersion);
			Console.WriteLine("    BuildID: " + QbeSAS.QbeClientVersion.CVSID);
			Console.WriteLine("    Copyright 2001-2006 Christian Hofstaedtler");
			Console.WriteLine("    Copyright 2001-2004 Andreas Stuetzner");
			Console.WriteLine("    ");
			Console.WriteLine("    About Qbe SAS: http://sas.qbe.ch/");
			Console.WriteLine("");
			Console.WriteLine("   **********************************************************************");
			Console.WriteLine("   *****          5  J A H R E  -  5  Y E A R S  -  5  A N S        *****");
			Console.WriteLine("   *****   T H E  T I M E  T O  C E L E B R A T E  H A S  C O M E   *****");
			Console.WriteLine("   **********************************************************************");	
			Console.WriteLine("");
			
			// -V (=nur Version), jetzt beenden
			if (args.Length > 0) if ((args[0] == "-help") || (args[0] == "--help"))
			{
				Console.WriteLine("   -> i believe in:");
				Console.WriteLine("      -V");
				Console.WriteLine("           display version and exit");
				Console.WriteLine("      -distapp [url]");
				Console.WriteLine("           download app from url and install it");
				Console.WriteLine("      -forcedistapp [url]");
				Console.WriteLine("           download app from url and install it, also if already installed");
				Console.WriteLine("      -start");
				Console.WriteLine("           service control manager passes this in and i start the service");
				return;
			}
			
			// -V (=nur Version), jetzt beenden
			if (args.Length > 0) if ((args[0] == "-V") || (args[0] == "--version"))
				return;

			// -distapp:
			if (args.Length > 1) if (args[0] == "-distapp")
			{
				Console.WriteLine("   -> Distributing an application. Please wait...");
				Console.WriteLine("");
//				Console.WriteLine("      " + args[1]);
				QbeSAS.ApplicationDistributor ad = new QbeSAS.ApplicationDistributor(args[1]);
				ad.ModeForce = false;
				ad.downloadAndInstallApplication();
				return;
			}
			
			// -forcedistapp:
			if (args.Length > 1) if (args[0] == "-forcedistapp")
			{
				Console.WriteLine("   -> Forcibly distributing an application. Please wait...");
				Console.WriteLine("");
//				Console.WriteLine("      " + args[1]);
				QbeSAS.ApplicationDistributor ad = new QbeSAS.ApplicationDistributor(args[1]);
				ad.ModeForce = true;
				ad.downloadAndInstallApplication();
				return;
			}

			// -debug:
			if (args.Length > 0) if (args[0] == "-debug")
			{
				Console.WriteLine("   -> Starting debug session...");
				QbeSAS.HttpService service = new QbeSAS.HttpService(7666,true);
				service.Run();
				return;
			}
			
			// -capturescreen:
			if (args.Length > 1) if (args[0] == "-capturescreen")
			{
				System.IO.MemoryStream stream = new System.IO.MemoryStream(200000);
				QbeSAS.CaptureScreen.SaveDesktopImage(stream,System.Drawing.Imaging.ImageFormat.Gif);
				byte[] buf = stream.ToArray();

				System.IO.FileStream fs = new System.IO.FileStream(args[1],System.IO.FileMode.OpenOrCreate);
				fs.Write(buf,0,buf.Length);
				fs.Close();
				return;
			}
			
			// -start:
			if (args.Length > 0) if (args[0] == "-start")
			{	
				System.ServiceProcess.ServiceBase[] ServicesToRun;
				ServicesToRun = new System.ServiceProcess.ServiceBase[] { new QbeService.Win32Service() };

				Console.WriteLine("   -> Starting service...");
				System.ServiceProcess.ServiceBase.Run(ServicesToRun);
				return;
			}

			Console.WriteLine("QbeSvc exited.\n");
		}
	}

	class Win32Service : System.ServiceProcess.ServiceBase
	{
		QbeSAS.HttpService service = null;

		public Win32Service()
		{
			this.ServiceName = "QbeSvc";
			
			this.CanStop = true;
			this.CanShutdown = true;
			this.CanHandlePowerEvent = true;
			this.CanPauseAndContinue = false;
			
			this.AutoLog = false;
		}
		
		protected override void OnStart(string[] args)
		{
			bool bUseSSL = false;
			// SSL aktivieren?
			if (args.Length > 0) if (args[0] == "-ssl")
				bUseSSL = true;

			// Qbe SAS Http Service starten
			service = new QbeSAS.HttpService(7666,bUseSSL);
			service.RunAsOwnThread();
		}		
		protected override void OnStop()
		{
			service.KillOwnThread();
		}
		protected override void OnShutdown()
		{
			service.runLogout();
		}
		protected override bool OnPowerEvent(PowerBroadcastStatus powerStatus)
		{
			if (powerStatus == PowerBroadcastStatus.QuerySuspend)
				service.runLogout();

			if ( 
					(powerStatus == PowerBroadcastStatus.QuerySuspendFailed) || 
					(powerStatus == PowerBroadcastStatus.ResumeSuspend) || 
					(powerStatus == PowerBroadcastStatus.ResumeCritical) 
				)
			{
				service.runLogin();
			}

			// always allow a suspend (or whatever) inquiry
			return true;
		}
		
	}
}
