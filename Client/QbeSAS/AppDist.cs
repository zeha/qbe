using System;
using System.Xml;
using System.Collections;

[assembly: System.Security.Permissions.RegistryPermissionAttribute(System.Security.Permissions.SecurityAction.RequestMinimum,
	All = "HKEY_LOCAL_MACHINE")]
[assembly: System.Security.Permissions.SecurityPermission( System.Security.Permissions.SecurityAction.RequestMinimum, ControlThread = true )]


namespace QbeSAS
{
	public class ApplicationDistributor
	{
		String ApplicationUrl = "";
		public bool ModeInstall = true;	// set to false to uninstall
		public bool ModeForce = false;
		public ApplicationConfig ApplicationDescription = null;
		public ApplicationDistributor() { }
		public ApplicationDistributor(String AppUrl) { ApplicationUrl = AppUrl; }
		public String ConfigUserAgentAdd = "";
		public String ConfigPlatformName = 
#if !UNIX
							"WIN32";
#else
							"UNIX";
#endif


		public void downloadAndInstallApplication()
		{
			Int32 oldVersion = -1;
			
			try {
				this.getApplicationConfig();
			} catch (Exception ex)
			{	ex=ex;
				Console.WriteLine("  ** FATAL: Error fetching Application Config: "+ex.ToString());
				return;
			}

			try {
				oldVersion = this.getAppDistStatus_Version();
			} catch (Exception ex)
			{	ex=ex;	}
			

			Console.WriteLine("  INFO: Upgrading from "+oldVersion.ToString()+" to "+this.ApplicationDescription.Version.ToString());

			try {
				// only install if old version is lower (or: app not installed -> oldversion = -1)
				if ( ( this.ModeForce == true ) || ( oldVersion < this.ApplicationDescription.Version ) )
					this.installApplication();
				else
					Console.WriteLine("  NOTE: Application/Version already installed!");
			} catch (Exception ex)
			{
				ex=ex;
				Console.WriteLine("  ** FATAL: Error installing Application: "+ex.ToString());
				return;
			}
		}

		public void getApplicationConfig()
		{
			// http://www.thecodeproject.com/csharp/xmlserialize.asp
			System.Xml.XmlTextReader rdr = new System.Xml.XmlTextReader(ApplicationUrl);
			System.Xml.Serialization.XmlSerializer x = new System.Xml.Serialization.XmlSerializer(typeof(ApplicationConfig));
			ApplicationDescription = (ApplicationConfig)x.Deserialize(rdr);
		}

		public bool returnApplicationConfigXML(System.IO.TextWriter textWriter)
		{
			try {
				System.Xml.Serialization.XmlSerializer x = new System.Xml.Serialization.XmlSerializer(typeof(ApplicationConfig));
				x.Serialize(textWriter,this.ApplicationDescription);
			} catch (Exception ex)
			{
				ex=ex;
				return false;
			}
			return true;
		}

		public void installApplication()
		{
			bool bAbort = false;
			
			if (ApplicationDescription == null)
				throw new Exception("No Application configuration specified.");

			if (ApplicationDescription.Platforms.Length == 0)
			{
				Console.WriteLine("  INFO: Applying application to all platforms.");
			} else {
				bool bFound = false;
				foreach (String Platform in ApplicationDescription.Platforms)
				{
					if (Platform == this.ConfigPlatformName)
					{
						bFound = true;
						break;
					}
				}
				if (!bFound)
				{
					throw new Exception("Application not to be applied to this platform.");
				} else {
					Console.WriteLine("  INFO: Applying application \"" + this.ApplicationDescription.PortableId + "\".");
				}
			}
			
			foreach(ApplicationDistAction Action in ApplicationDescription.InstallActions)
			{
				// expand environment stuff first...
				String ActionObject = ""; if (Action.ActionObject != null) ActionObject = Environment.ExpandEnvironmentVariables(Action.ActionObject);
				String ActionTarget = ""; if (Action.ActionTarget != null) ActionTarget = Environment.ExpandEnvironmentVariables(Action.ActionTarget);

				switch (Action.ActionType)
				{
					case "MakeDirectory":
						// ActionObject = new dir name
						Console.WriteLine("    Create Dir: "+ActionObject);
						try {
							if (!System.IO.Directory.Exists(ActionObject))
								System.IO.Directory.CreateDirectory(ActionObject);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "DeleteDirectory":
						// ActionObject = local dir name
						Console.WriteLine("    Delete Dir: "+ActionObject);
						try {
							if (System.IO.Directory.Exists(ActionObject))
								System.IO.Directory.Delete(ActionObject);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "CopyFile":
						// ActionObject contains local file name to copy
						// ActionTarget contains new local file name
						Console.WriteLine("    Copy: "+ActionObject+" to: "+ActionTarget);
						try {
							System.IO.File.Copy(ActionObject,ActionTarget);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "DeleteFile":
						// ActionObject contains local file name to delete
						Console.WriteLine("    Delete: "+ActionObject);
						try {
							if (System.IO.File.Exists(ActionObject))
								System.IO.File.Delete(ActionObject);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "FetchFile":
						// ActionObject contains URL
						// ActionTarget contains local file name
						Console.WriteLine("    Fetch: "+ActionObject+" to: "+ActionTarget);
						if (!execFetchFile(ActionObject,ActionTarget))
							if (Action.FailureIsFatal) bAbort = true;
						break;
					case "AppendTextFile":
						// ActionObject contains local source file name
						// ActionTarget contains local destination file name
						Console.WriteLine("    Append (Text): "+ActionObject+" to: "+ActionTarget);
						try {
							if (!System.IO.File.Exists(ActionTarget))
							{
								System.IO.StreamWriter sw = System.IO.File.CreateText(ActionTarget);
							}
							System.IO.StreamReader sr = System.IO.File.OpenText(ActionObject);
							using (System.IO.StreamWriter sw = System.IO.File.AppendText(ActionTarget))
							{
								String line = "";
								while ( (line = sr.ReadLine()) != null )
								{
									sw.WriteLine(line);
								}
								sw.Close();
							}
							sr.Close();
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "AppendBinaryFile":
						// ActionObject contains local source file name
						// ActionTarget contains local destination file name
						Console.WriteLine("    Append (Binary): "+ActionObject+" to: "+ActionTarget);
						try {
							if (!System.IO.File.Exists(ActionTarget))
							{
								System.IO.FileStream sw = System.IO.File.Create(ActionTarget);
								sw.Close();
							}
							System.IO.FileStream sr = System.IO.File.Open(ActionObject,System.IO.FileMode.Open);
							using (System.IO.FileStream sw = System.IO.File.Open(ActionTarget,System.IO.FileMode.Append))
							{
								byte[] b = new byte[4096];
								int bytesRead = 0;
								while ( (bytesRead = sr.Read(b,0,b.Length)) > 0 )
								{
									sw.Write(b, 0, bytesRead);
								}
								sw.Close();
							}
							sr.Close();
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "Execute":
						// ActionObject contains local file name
						// ActionTarget contains parameters
						Console.WriteLine("    Execute: "+ActionObject+" "+ActionTarget);
						try {
							System.Diagnostics.Process.Start(ActionObject,ActionTarget);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					case "ExecuteAndWait":
						// ActionObject contains local file name
						// ActionTarget contains parameters
						Console.WriteLine("    ExecuteAndWait: "+ActionObject+" "+ActionTarget);
						try {
							System.Diagnostics.Process p = System.Diagnostics.Process.Start(ActionObject,ActionTarget);
							if (Action.WaitMaxTime == 0)
								p.WaitForExit();
							else
								p.WaitForExit(Action.WaitMaxTime*1000);
						} catch (Exception ex) { ex=ex; if (Action.FailureIsFatal) bAbort = true; }
						break;
					default:
						// dont know what to do, just ignore it
						break;
				}
			
				if (bAbort)
				{
					Console.WriteLine("  FATAL: Application Distribution aborted.");
					break;
				}
			}

			if (!bAbort)
			{
				Console.WriteLine("  SUCCESS: Application applied.");
				saveAppDistStatus();
			}
		}

		internal bool execFetchFile(String szURL, String szLocalFilename)
		{
			System.Net.WebClient Client = new System.Net.WebClient();
			
			try {
				Client.Headers.Add("User-Agent", "QbeService/" + QbeSAS.QbeClientVersion.ClientVersion + " (ApplicationDistributor; " + this.ConfigPlatformName + "; " + this.ConfigUserAgentAdd + ")");
				Client.DownloadFile(szURL,szLocalFilename);

			} catch (Exception ex)
			{
				ex=ex;
				return false;
			}
			return true;
		}

		internal Int32 getAppDistStatus_Version()
		{
			Microsoft.Win32.RegistryKey appcfgkey = Microsoft.Win32.Registry.LocalMachine.OpenSubKey("SOFTWARE\\Qbe\\Applications\\" + this.ApplicationDescription.PortableId);
			if (appcfgkey != null)
				return Int32.Parse(appcfgkey.GetValue("Version").ToString());
			else
				return -1;
		}
		
		internal void saveAppDistStatus()
		{
			System.Threading.Thread.CurrentThread.CurrentCulture = new System.Globalization.CultureInfo( "en-US", false );
			System.Threading.Thread.CurrentThread.CurrentUICulture = new System.Globalization.CultureInfo( "en-US", false );
			
			Microsoft.Win32.RegistryKey appcfgkey = Microsoft.Win32.Registry.LocalMachine.CreateSubKey("SOFTWARE\\Qbe\\Applications\\" + this.ApplicationDescription.PortableId);
			appcfgkey.SetValue("DisplayName",this.ApplicationDescription.Name.ToString());
			appcfgkey.SetValue("Version",this.ApplicationDescription.Version.ToString());
			appcfgkey.SetValue("URL",this.ApplicationUrl.ToString());
			appcfgkey.SetValue("InstallDate",System.DateTime.Now.ToString());
		}
	}

	public class ApplicationConfig
	{
		public ApplicationConfig()
		{
			// do nothing
		}
		public ApplicationDistAction[] InstallActions = new ApplicationDistAction[0];
		public ApplicationDistAction[] UninstallActions = new ApplicationDistAction[0];
		public String[] Platforms = new String[0];
		public String Name = "";
		public Int32 Version = 0;
		public String PortableId = "";

		public bool AddInstallAction(ApplicationDistAction action)
		{
			int size = this.InstallActions.Length+1;
			QbeSAS.ApplicationDistAction[] newA = new ApplicationDistAction[size];
			this.InstallActions.CopyTo(newA,0);
			newA[size-1] = action;
			this.InstallActions = newA;
			return true;
		}
		public bool AddUninstallAction(ApplicationDistAction action)
		{
			int size = this.UninstallActions.Length+1;
			QbeSAS.ApplicationDistAction[] newA = new ApplicationDistAction[size];
			this.UninstallActions.CopyTo(newA,0);
			newA[size-1] = action;
			this.UninstallActions = newA;
			return true;
		}
	}

	public class ApplicationDistAction
	{
		public ApplicationDistAction()
		{
			// do nothing
		}
		public string ActionType = "";
		public string ActionObject = "";
		public string ActionTarget = "";
		public bool FailureIsFatal = true;
		public Int32 WaitMaxTime = 0;
	}
}
