using System;
using QbeSAS;

namespace QTests
{
	class XMain
	{
		[STAThread] static void Main()
		{
			System.IO.TextWriter stringWriter = new System.IO.StreamWriter("app.xml");

			QbeSAS.ApplicationDistributor a = new QbeSAS.ApplicationDistributor();
			QbeSAS.ApplicationConfig appcfg = new QbeSAS.ApplicationConfig();
			QbeSAS.ApplicationDistAction act = new QbeSAS.ApplicationDistAction();

			act.ActionType = "FetchFile";
			act.ActionObject = "fup";
			act.ActionTarget = "bar";
			appcfg.AddInstallAction(act);

			act.ActionType = "exec";
			appcfg.AddInstallAction(act);
			
			appcfg.Platforms = new String[2];
			appcfg.Platforms[0] = "WIN32";
			appcfg.Platforms[1] = "UNIX";
			appcfg.Version = 1;
			appcfg.Name = "foo";
			appcfg.PortableId = "FOO";
				
			a.ApplicationDescription = appcfg;
			
			a.returnApplicationConfigXML(stringWriter);
			stringWriter.Close();

			QbeSAS.ApplicationDistributor ad = new QbeSAS.ApplicationDistributor("getapp.xml");
			ad.downloadAndInstallApplication();

			ad = new QbeSAS.ApplicationDistributor("getapp.xml");
			ad.ModeForce = true;
			ad.downloadAndInstallApplication();
		}
	}
}
