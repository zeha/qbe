using System;
using System.Reflection;
using System.Runtime.InteropServices;
using Microsoft.Win32;

[assembly:ComVisible(true)]
namespace QbeSAS
{
	/// 
	public class DataStore
	{
		private RegistryKey regKey;
		public DataStore()
		{
			regKey = Registry.CurrentUser.OpenSubKey("SOFTWARE\\Qbe\\SAS\\Client\\UserData", true);
			if (regKey == null)
				regKey = Registry.CurrentUser.CreateSubKey("SOFTWARE\\Qbe\\SAS\\Client\\UserData");
		}

		public bool SaveData(String username, String password, bool savePassword)
		{
			regKey.SetValue("Username",username);
			regKey.SetValue("Password","");
			if (savePassword == true)
				regKey.SetValue("Password",password);
			return true;
		}

		public String getUsername()
		{
			return (String)regKey.GetValue("Username","");
		}
		public String getPassword()
		{
			return (String)regKey.GetValue("Password","");
		}

		public bool NetworkLogon(String username, String password)
		{
			return this.ExecApp("cmd.exe","/c \"" + Environment.GetEnvironmentVariable("SystemRoot") + "\\System32\\Qbe\\qbelogon.cmd\" \"" + username + "\" \"" + password + "\"");
		}
		public bool NetworkLogout(String username)
		{
			return this.ExecApp("cmd.exe","/c \"" + Environment.GetEnvironmentVariable("SystemRoot") + "\\System32\\Qbe\\qbelogout.cmd\"  \"" + username + "\"");
		}
		
		private bool ExecApp(String application, String arguments)
		{
			try {
				System.Diagnostics.ProcessStartInfo si = new System.Diagnostics.ProcessStartInfo(application,arguments);
				si.WindowStyle = System.Diagnostics.ProcessWindowStyle.Minimized;
				System.Diagnostics.Process.Start(si);
				return true;
			} catch (System.ComponentModel.Win32Exception ex)
			{
				System.Windows.Forms.MessageBox.Show("Could not start a required file for the login/logout procedure.\nDetails:\n"+ex.ToString(),"Qbe SAS Client",System.Windows.Forms.MessageBoxButtons.OK,System.Windows.Forms.MessageBoxIcon.Error);
				return false;
			}
		}
	}
}

