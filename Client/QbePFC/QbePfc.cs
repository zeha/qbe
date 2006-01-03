//
// $Id: StartX.cs 68 2004-04-22 09:13:42Z ch $
//
// (C) Copyright 2001-2004 Christian Hofstaedtler

using System;
using System.Collections;
using System.Diagnostics;
using System.Reflection;
using ComReg;

namespace ComReg
{
	/// Startup Code fuer den Pre-Flight-Check
	public class Start
	{
		/// Einsprungspunkt fuer den PreFlightCheck 
		[STAThread]
		public static void Main(string[] args)
		{
			// Parameter nach einem "/c" werden an unser ComReg Modul uebergeben
			if ( (args.Length > 0) && (args[0] == "/c") )
			{
				ComReg.ComRegister.ComRegisterMain(args);
			} else 	// /u upgraded client files
			if ( (args.Length > 1) && (args[0] == "/u") )
			{
				// ois schliassn
				QbeSAS.PFC.pfc_setup(false);

				// files renamen
				UpgradeFiles(args[1]);
				
				QbeSAS.PFC.pfc_setup(false);

			} else 	// /k beendet Qbe Applications
			if ( (args.Length > 0) && (args[0] == "/k") )
			{
				QbeSAS.PFC.pfc_setup(false);
			
			} else 	// /K beendet Qbe Applications + loescht den Service
			if ( (args.Length > 0) && (args[0] == "/K") )
			{
				QbeSAS.PFC.pfc_setup(true);
			
			} else 	// /I installiert den service neu
			if ( (args.Length > 1) && (args[0] == "/I") )
			{
				try {
					// just in case: remove the previously installed service
					QbeSAS.PFC pfc = new QbeSAS.PFC();
					pfc.execKillService(true);
				} catch (Exception ex)
				{	ex=ex; }
				
				try {
					// install new service
					QbeSAS.WinServiceAPI.InstallService(args[1]);
				} catch (Exception ex)
				{	ex=ex;
					showMsg("Qbe SAS Client Service Installation fehlgeschlagen. Das System gab folgenden Fehler zurück:\n"+ex.ToString());
				}
				
			} else 	// /R loescht den Service
			if ( (args.Length > 0) && (args[0] == "/R") )
			{
				QbeSAS.PFC pfc = new QbeSAS.PFC();
				pfc.execKillService(true);
			} else 	// /? zeigt die parameter an
			if ( (args.Length > 0) && (args[0] == "/?") )
			{
				ShowUsage();
			} else {
				// kenn i ned
				ShowUsage();
			}
		}
		
		static void UpgradeFiles(String upgradeFolder)
		{
			// lest alle files im ordner
			System.IO.DirectoryInfo dir = new System.IO.DirectoryInfo(upgradeFolder);
			foreach(System.IO.FileInfo file in dir.GetFiles("*.NEW"))
			{
				//System.IO.File.Rename(file.GetFullPath(),file.GetFileNameWithoutExtension());
				String oldName = file.DirectoryName+"\\"+file.Name;
				String newName = System.IO.Path.ChangeExtension(oldName,null);
				try 
				{
					System.IO.File.Delete(newName);
				} 
				catch (Exception ex) { ex=ex; }
				System.IO.Directory.Move(oldName,newName);
			}
		}
		
		/// Shows the String msg in a nice dialog box
		static void showMsg(String msg)
		{
			System.Windows.Forms.MessageBox.Show(msg,"Qbe Software Setup",System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Information);
		}	
		
		/// Shows the (main) parameters for QbePFC
		static void ShowUsage()
		{
			String msg;
			msg = "Qbe PFC - Software Setup Helper\n";
			msg += "Copyright (C) Christian Hofstaedtler 2004\n";
			msg += "\n";
			msg += "Syntax: QbePFC /?\n   Show this help screen.\n";
			msg += "Syntax: QbePFC /c [options]\n   Pass [options] to comreg. Try QbePFC /c /?.\n";
			msg += "Syntax: QbePFC /u [directory]\n   Upgrade *.new files in folder [directory].\n";
			msg += "Syntax: QbePFC /k\n   Close running Qbe Applications without removing the service.\n";
			msg += "Syntax: QbePFC /K\n   Close running Qbe Applications and remove the service.\n";
			msg += "Syntax: QbePFC /R\n   Stop and remove QbeSvc.\n";
			msg += "Syntax: QbePFC /I [prog]\n   Install QbeSvc with [prog] as the ImagePath.\n";
			showMsg(msg);
		}
	}
}
