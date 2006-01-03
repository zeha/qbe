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
			bool bUseSSL = false;
			// SSL aktivieren?
			if (args.Length > 0) if (args[0] == "-ssl")
				bUseSSL = true;

			// Version ausgeben
			Console.WriteLine("Qbe SAS Client (XPlat) - Copyright 2001-2004 Christian Hofstaedtler");
			Console.WriteLine("                         Copyright 2001-2004 Andreas Stuetzner");
			Console.WriteLine("    Release: " + QbeSAS.QbeClientVersion.ClientVersion);
			Console.WriteLine("    Buildid: " + QbeSAS.QbeClientVersion.CVSID);
			Console.WriteLine("    About Qbe SAS: http://sas.qbe.ch/");
			
			// -V (=nur Version), jetzt beenden
			if (args.Length > 0) if (args[0] == "-V")
				return;
			
			Console.WriteLine("  SSL: " + (bUseSSL == true ? "enabled" : "disabled"));
			// Qbe SAS Http Service starten
			QbeSAS.HttpService service = new QbeSAS.HttpService(7666,bUseSSL);
			service.Run();
		}
	}
}
