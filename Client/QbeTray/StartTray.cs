//
// $Id: StartTray.cs 174 2004-09-29 23:18:03Z ch $
//
// (C) Copyright 2001-2004 Christian Hofstaedtler

using System;
using System.Collections;
using System.Diagnostics;

namespace QbeSAS
{
	/// Code fuer den Client-Start, verwendet allerhand QbeSAS:: Methoden und QbeSAS::Splash
	public class StartTray
	{
		bool runningInAutomaticMode = false;
		QbeSAS.Splash splashForm = null;

		/// Zeigt die Fehlermeldung wenn "qbe-auth" nicht aufgeloest werden konnte
		public void ShowDnsError()
		{
			if (!runningInAutomaticMode)
				System.Windows.Forms.MessageBox.Show("Qbe SAS Client kann den Loginserver \"qbe-auth\" nicht finden.\nStellen Sie sicher, dass Ihr Netzwerkkabel angesteckt ist und Ihr PC die richtige IP-Adresse zugewiesen bekommt.", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Exclamation);
		} 

		/// Background-Thread fuer das GUI
		void FormThread()
		{
			try 
			{
				splashForm.lblVersion.Text = "Version "+QbeSAS.QbeClientVersion.ClientVersion;

				System.Windows.Forms.Application.Run(splashForm);
			} 
			catch (Exception ex)
			{
				ex=ex; //shut up!
//	don't!
//				System.Windows.Forms.MessageBox.Show("FormThread Error:\nMsg: "+ex.Message +"\nLocation: "+ex.StackTrace);
			}
		}

		/// Die Funktion die den Client initialisiert
		/// bAuto == true: keine Fehler zeigen und einfach beenden, wird normalerweise beim Systemstart verwendet
		public void StartTrayExecute(bool bAuto)
		{
			System.Threading.Thread t = null;
			QbeSAS.ClientUI sasClientUI = null;
			System.ServiceProcess.ServiceController serviceController1 = null;
			
			splashForm = new QbeSAS.Splash();			

			// Client UI initialisieren
			try 
			{
				sasClientUI = new QbeSAS.ClientUI(bAuto);
			} 
			catch (Exception ex)
			{
				System.Windows.Forms.MessageBox.Show("Error initializing QbeLib.\n"+ex.StackTrace);
			}

			runningInAutomaticMode = bAuto;
			bool bAllOk = true;

			try 
			{
				t = new System.Threading.Thread(new System.Threading.ThreadStart(FormThread));
				t.Start();
				System.Windows.Forms.Application.DoEvents();
				System.Threading.Thread.Sleep(500);
			} 
			catch (Exception ex)
			{
				System.Windows.Forms.MessageBox.Show("Error initializing QbeTray [Stage 1].\n"+ex.StackTrace);
			}

			splashForm.stLbl_NetInit.Font = new System.Drawing.Font(splashForm.stLbl_NetInit.Font, System.Drawing.FontStyle.Bold);
			System.Windows.Forms.Application.DoEvents();
         		System.Threading.Thread.Sleep(5000);

			try
			{
				System.Net.IPHostEntry QbeAuthHE = System.Net.Dns.GetHostByName("qbe-auth");
				splashForm.pctNetInit.Image = splashForm.pctTpl_Ok.Image;
			} 
			catch (Exception ex)
			{	ex = ex;	// keep compiler quiet
				bAllOk = false;
				splashForm.pctNetInit.Image = splashForm.pctTpl_Fail.Image;
				ShowDnsError();
				if (runningInAutomaticMode)
				{
					System.Windows.Forms.Application.Exit();
					return;
				}
			} 
			splashForm.stLbl_NetInit.Font = new System.Drawing.Font(splashForm.stLbl_NetInit.Font, System.Drawing.FontStyle.Regular);
			System.Windows.Forms.Application.DoEvents();
			splashForm.stLbl_Service.Font = new System.Drawing.Font(splashForm.stLbl_Service.Font, System.Drawing.FontStyle.Bold);
			System.Windows.Forms.Application.DoEvents();

			try 
			{
				serviceController1 = new System.ServiceProcess.ServiceController("QbeSvc");
				serviceController1.Refresh();
			} 
			catch (Exception ex)
			{
				ex = ex;	// keep compiler quiet
				bAllOk = false;
				if (!runningInAutomaticMode)
					System.Windows.Forms.MessageBox.Show("The Windows Service Manager returend an error while requesting a connection to the Qbe SAS Client service. Try rebooting the computer or reinstalling the application.", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Error);
				else
				{
					System.Windows.Forms.Application.Exit();
					return;
				}
			}

			try 
			{
				if (serviceController1 != null)
				{
					if (serviceController1.Status != System.ServiceProcess.ServiceControllerStatus.Running)
						serviceController1.Start();
					splashForm.pctService.Image = splashForm.pctTpl_Ok.Image;
				} 
				else 
				{
					throw new Exception();
				}
			} 
			catch (Exception ex)
			{
				ex = ex;	// keep compiler quiet
				
				splashForm.pctService.Image = splashForm.pctTpl_Fail.Image;
				bAllOk = false;
				if (!runningInAutomaticMode)
					System.Windows.Forms.MessageBox.Show("Could not talk to Qbe SAS Client service. Try rebooting the computer or reinstalling the application.", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Error);
				else
				{
					System.Windows.Forms.Application.Exit();
					return;
				}
			}

			splashForm.stLbl_Service.Font = new System.Drawing.Font(splashForm.stLbl_Service.Font, System.Drawing.FontStyle.Regular);
			System.Windows.Forms.Application.DoEvents();
			splashForm.stLbl_Client.Font = new System.Drawing.Font(splashForm.stLbl_Client.Font, System.Drawing.FontStyle.Bold);
			System.Windows.Forms.Application.DoEvents();

			if ( bAllOk )
			{
				sasClientUI.ImportSettings();
				if (sasClientUI.DisplayClientStartup())
					splashForm.pctClient.Image = splashForm.pctTpl_Ok.Image;
				else
					splashForm.pctClient.Image = splashForm.pctTpl_Fail.Image;
			}
			else
				splashForm.pctClient.Image = splashForm.pctTpl_Fail.Image;

			splashForm.stLbl_Client.Font = new System.Drawing.Font(splashForm.stLbl_Client.Font, System.Drawing.FontStyle.Regular);
			System.Windows.Forms.Application.DoEvents();
			System.Threading.Thread.Sleep(1000);

			splashForm.Close();

			System.Diagnostics.Process[] oldTrays = System.Diagnostics.Process.GetProcessesByName("QbeTray");
			if (oldTrays.Length > 1)
			{
				System.Windows.Forms.MessageBox.Show("Qbe SAS Client läuft bereits. Diese Instanz wird beendet.","Qbe SAS Client",System.Windows.Forms.MessageBoxButtons.OK,System.Windows.Forms.MessageBoxIcon.Exclamation);
				return;
			}

			System.Windows.Forms.Application.DoEvents();
//			System.Windows.Forms.Application.Exit();			
			QbeSAS.Tray trayIcon = new QbeSAS.Tray();
			System.Windows.Forms.Application.Run();
		}

		/// Einsprungspunkt fuer den Win32 Qbe SAS Client -- QbeTray
		/// -auto wird als Parameter akzeptiert, und uebergibt dies an die StartXExec()
		[STAThread]
		public static void Main(string[] args)
		{
			bool bAuto = false;

			try 
			{
				if (args.Length == 1)
					if (args[0].Equals("-auto"))
						bAuto = true;
			} 
			catch (Exception ex)
			{
				System.Windows.Forms.MessageBox.Show(ex.StackTrace);
			}

			StartTray app = new StartTray();
			app.StartTrayExecute(bAuto);
		}
	}
}
