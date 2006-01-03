using System;

namespace QbeSAS
{
	/// Das gesamte Benutzerinterface des Win32 Clients. 
	public class ClientUI
	{
		private bool Silent;
		private String appPath = "";

		/// Initialisert ClientUI::. Initialisert QbeDirectory (appPath) und this.Silent
		public ClientUI(bool silent)
		{
			appPath = System.Environment.SystemDirectory;
			if (!appPath.EndsWith("\\"))
				appPath += "\\";
			appPath += "Qbe\\";

			this.Silent = silent;
		}

		/// Ruft einen Browser mit dem Web-Interface Login auf
		public bool DisplaySASLogin()
		{
			try 
			{
				System.Diagnostics.Process.Start( "http://qbe-auth/modules/core/checklogin.php" );
				return true;
			} 
			catch (Exception ex)
			{	ex=ex;
				if (!Silent)
                    System.Windows.Forms.MessageBox.Show("Could not start the default browser to view the Qbe SAS login page. Operation aborted.","Qbe SAS Client",System.Windows.Forms.MessageBoxButtons.OK,System.Windows.Forms.MessageBoxIcon.Error);
				return false;
			}

		}

		/// Ruft die SASClient.hta auf
		public bool DisplayClientLogin()
		{
			System.Diagnostics.ProcessStartInfo si = new System.Diagnostics.ProcessStartInfo();
			si.WorkingDirectory = appPath;
			si.UseShellExecute = true;
			si.FileName = appPath + "SASClient.hta";
			try 
			{
				System.Diagnostics.Process.Start(si);
				return true;
			} 
			catch (Exception ex)
			{	ex=ex;
				if (!Silent)
					System.Windows.Forms.MessageBox.Show("Could not find required file \"SASClient.hta\".", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Error);
				return false;
			}
		}

		/// Ruft die startup.hta auf
		public bool DisplayClientStartup()
		{
			System.Diagnostics.ProcessStartInfo si = new System.Diagnostics.ProcessStartInfo();
			si.WorkingDirectory = appPath;
			si.UseShellExecute = true;
			si.FileName = appPath + "startup.hta";
			try 
			{
				System.Diagnostics.Process.Start(si);
				return true;
			} 
			catch (Exception ex)
			{	ex=ex;
				if (!Silent)
					System.Windows.Forms.MessageBox.Show("Could not find required file \"startup.hta\".", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Error);
				return false;
			}
		}

		/// Importiert die Einstellungen (Proxy usw.) in die Registry. Dazu wird die Datei SASClient.reg einfach mit dem regedit importiert.
		public bool ImportSettings()
		{
			System.Diagnostics.ProcessStartInfo si = new System.Diagnostics.ProcessStartInfo();
			si.WorkingDirectory = appPath;
			si.UseShellExecute = false;
			si.FileName = "regedit.exe";
			si.Arguments = "/s \"" + appPath + "SASClient.reg\"";
			try 
			{
				System.Diagnostics.Process.Start(si);
				return true;
			} 
			catch (Exception ex)
			{	ex=ex;
				if (!Silent)
					System.Windows.Forms.MessageBox.Show("Could not find required file \"iLogin.reg\".", "Qbe SAS Client", System.Windows.Forms.MessageBoxButtons.OK, System.Windows.Forms.MessageBoxIcon.Error);
				return false;
			}
		}

	}
}

