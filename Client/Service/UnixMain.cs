using System;

namespace QbeService
{
	/// Stellt die Umgebung fuer den XPlat-Client zur Verfuegung.
	class UnixMain
	{
		/// Einsprungsfunktion fuer den XPlat-Client
		/// -ssl aktiviert SSL
		/// -V zeigt die Versionsnummer
		[STAThread]
		static void Main(string[] args)
		{
			bool EnableSSL = false;
			String AuthServer = "";
			if (args.Length > 0)
			{
				for(int i = 0; i < args.Length; i++)
				{
					bool ArgumentOk = false;
					
					// SSL aktivieren?
					if (args[i] == "--ssl")
					{
						EnableSSL = true;
						ArgumentOk = true;
					}
					
					// anderer Authentication Server
					if ((args[i] == "--host") && (args.Length >= (i+1)))
					{
						++i;
						AuthServer = args[i];
						ArgumentOk = true;
					}
					
					// sollen wir ein pidfile schreiben?
					if ((args[i] == "--pidfile") && (args.Length >= (i+1)))
					{
						++i;
						try {
						using (System.IO.StreamWriter sw = new System.IO.StreamWriter(args[i])) 
							sw.WriteLine(System.Diagnostics.Process.GetCurrentProcess().Id.ToString());
						
							ArgumentOk = true;
						} catch (Exception ex)
						{
							Console.WriteLine(ex.StackTrace);
						}
					}

					if (!ArgumentOk)
					{
						Console.WriteLine("Warning: argument " + i.ToString() + " not recognised.");
					}
				}
			}

			// Version ausgeben
			Console.WriteLine("     ____  _               Qbe SAS Client (XPlat) " + QbeSAS.QbeClientVersion.ClientVersion);
			Console.WriteLine("    / __ \\| |              Copyright 2001-2006 Christian Hofstaedtler");
			Console.WriteLine("   | |  | | |__   ___      Copyright 2001-2004 Andreas Stuetzner");
			Console.WriteLine("   | |  | | '_ \\ / _ \\     " + QbeSAS.QbeClientVersion.CVSID);
			Console.WriteLine("   | |__| | |_) |  __/     ");
			Console.WriteLine("    \\___\\_\\_.__/ \\___|     About Qbe SAS: http://sas.qbe.ch/");
			Console.WriteLine("                           \"the school network solution (tm)\"");
			Console.WriteLine("");
			Console.WriteLine("   **********************************************************************");
			Console.WriteLine("   *****          5  J A H R E  -  5  Y E A R S  -  5  A N S        *****");
			Console.WriteLine("   *****   T H E  T I M E  T O  C E L E B R A T E  H A S  C O M E   *****");
			Console.WriteLine("   **********************************************************************");
			Console.WriteLine("");
			Console.WriteLine("   Limitations of XPlat Code:");
			Console.WriteLine("     * NO time synchronisation (use NTP instead)");
			Console.WriteLine("     * SSL is OFF by default, use --ssl to enable");
			Console.WriteLine("");
			
			// -V (=nur Version), jetzt beenden
			if (args.Length > 0) if (args[0] == "-V")
				return;
			
			Console.WriteLine("   To log in, please visit the URI mentioned below.");
			// Qbe SAS HTTP Service starten
			QbeSAS.HttpService service = new QbeSAS.HttpService(7666,EnableSSL,AuthServer);
			service.Run();
		}
	}
}
