using System;
using System.Collections;
using System.Diagnostics;
using System.Runtime.InteropServices;

[assembly:ComVisible(false)]
namespace QbeSAS
{
	public class Tray
	{
		//		System.Windows.Forms.NotifyIcon notifyIcon;
		TrayNotify notifyIcon;
		System.Windows.Forms.Timer timer;
		System.Resources.ResourceManager resources;
		
		int prevConnstate = 0; //-1;
		int prevInetstate = 1;

		public Tray() 
		{
			resources = new System.Resources.ResourceManager("Resource",System.Reflection.Assembly.GetExecutingAssembly());
			notifyIcon = new TrayNotify(); //System.Windows.Forms.NotifyIcon();
			notifyIcon.Text = "Qbe Tray";
			notifyIcon.Icon = (System.Drawing.Icon)resources.GetObject("IconNoConnection");
			/*			notifyIcon.Visible = true;
						notifyIcon.MouseDown += new System.Windows.Forms.MouseEventHandler(notifyIcon_MouseDown);
			*/	
			notifyIcon.SetTrayIcon();

			timer = new System.Windows.Forms.Timer();
			timer.Interval = 30000;
			timer.Tick += new EventHandler(timer_Tick);
			timer.Enabled = true;

			Microsoft.Win32.SystemEvents.SessionEnding += new Microsoft.Win32.SessionEndingEventHandler(SystemEvents_SessionEnding);
			Microsoft.Win32.SystemEvents.PowerModeChanged += new Microsoft.Win32.PowerModeChangedEventHandler(SystemEvents_PowerModeChanged);
		}


		private void timer_Tick(object sender, EventArgs e)
		{
			int inetState = QbeSAS.SysState.queryServiceAgentInt("internetstate");
			int connState = QbeSAS.SysState.queryServiceAgentInt("connectionstate");
			notifyIcon.Text = QbeSAS.SysState.getStateString(connState,inetState,QbeSAS.SysState.queryServiceAgentStr("username"));
			switch (connState)
			{
				case 0:
				{
					if (inetState == 0)
						notifyIcon.Icon = (System.Drawing.Icon)resources.GetObject("IconInetFree");
					if (inetState == 1)
						notifyIcon.Icon = (System.Drawing.Icon)resources.GetObject("IconInetLock");
					break;
				}
				case 1:
				{
					notifyIcon.Icon = (System.Drawing.Icon)resources.GetObject("IconNoConnection");
					break;
				}
				case -1:
				default:
				{
					notifyIcon.Icon = (System.Drawing.Icon)resources.GetObject("IconNoConnection");
					break;
				}
			}
			notifyIcon.UpdateTrayIcon();
			
/*			if ( (prevConnstate != connState) && (connState != 0) )
			{
				notifyIcon.ShowTrayBalloon("Qbe SAS Client: Fehler", "Verbindung zum Systemdienst verloren.", TrayNotify.NIIF_ERROR);
			} 
*/			

			if ( (prevInetstate != inetState) && (connState == 0) )
			{
				if (inetState == 0)
				{
					notifyIcon.ShowTrayBalloon("Qbe SAS Client", "Verbindung zum Internet wurde durch das System freigegeben.", TrayNotify.NIIF_INFO);
				}
				if (inetState == 1)
				{
					notifyIcon.ShowTrayBalloon("Qbe SAS Client", "Verbindung zum Internet wurde durch das System gesperrt.", TrayNotify.NIIF_INFO);
				}
			}

			prevConnstate = connState;
			prevInetstate = inetState;
		}

		private void SystemEvents_SessionEnding(object sender, Microsoft.Win32.SessionEndingEventArgs e)
		{
			QbeSAS.SysState.runLogout();
		}

		private void SystemEvents_PowerModeChanged(object sender, Microsoft.Win32.PowerModeChangedEventArgs e)
		{
			if (e.Mode == Microsoft.Win32.PowerModes.Resume) 
			{ 
				QbeSAS.SysState.runLogin();
			}
			else if (e.Mode == Microsoft.Win32.PowerModes.Suspend)
			{
				QbeSAS.SysState.runLogout();
			}
		}
	}
}
