using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	/// Eigene TrayNotify Klasse die auch Balloon Tooltips anzeigen kann,
	/// und nicht unbedingt an ein richtiges Fenster gebunden sein muss.
	public class TrayNotify : System.Windows.Forms.Form
	{
		#region Windows MSG defines
		private const int WM_LBUTTONDOWN                  =0x0201;
		private const int WM_LBUTTONUP                    =0x0202;
		private const int WM_LBUTTONDBLCLK                =0x0203;
		private const int WM_RBUTTONDOWN                  =0x0204;
		private const int WM_RBUTTONUP                    =0x0205;
		private const int WM_RBUTTONDBLCLK                =0x0206;
		private const int WM_MBUTTONDOWN                  =0x0207;
		private const int WM_MBUTTONUP                    =0x0208;
		private const int WM_MBUTTONDBLCLK                =0x0209;
		#endregion

		#region NotifyIcon API Constants, Calls and Structs
		internal readonly static int WM_NOTIFY_TRAY = 0x0400 + 2001;
		internal readonly int uID = 4711;

		/// Icon: keines (shellapi.h)
		public const int NIIF_NONE = 0x00;
		/// Icon: Information (shellapi.h)
		public const int NIIF_INFO = 0x01;
		/// Icon: Achtung (shellapi.h)
		public const int NIIF_WARNING = 0x02;
		/// Icon: Fehler (shellapi.h)
		public const int NIIF_ERROR = 0x03;

		private const int NIF_MESSAGE = 0x01;
		private const int NIF_ICON = 0x02;
		private const int NIF_TIP = 0x04;
		private const int NIF_STATE = 0x08;
		private const int NIF_INFO = 0x10;

		private const int NIM_ADD = 0x00;
		private const int NIM_MODIFY = 0x01;
		private const int NIM_DELETE = 0x02;
		private const int NIM_SETFOCUS = 0x03;
		private const int NIM_SETVERSION = 0x04;

		private const int NIS_HIDDEN = 0x01;
		private const int NIS_SHAREDICON = 0x02;

		private const int NOTIFYICON_OLDVERSION = 0x00;
		private const int NOTIFYICON_VERSION = 0x03;

		[DllImport("shell32.dll", EntryPoint="Shell_NotifyIcon")]
		private static extern bool Shell_NotifyIcon (
			int dwMessage,
			ref NOTIFYICONDATA lpData
			);

		[StructLayout(LayoutKind.Sequential)]
		private struct NOTIFYICONDATA 
		{
			internal int cbSize;
			internal IntPtr hwnd;
			internal int uID;
			internal int uFlags;
			internal int uCallbackMessage;
			internal IntPtr hIcon;
			[MarshalAs(UnmanagedType.ByValTStr, SizeConst=0x80)]
			internal string szTip;
			internal int dwState;
			internal int dwStateMask;
			[MarshalAs(UnmanagedType.ByValTStr, SizeConst=0xFF)]
			internal string szInfo;
			internal int uTimeoutAndVersion;
			[MarshalAs(UnmanagedType.ByValTStr, SizeConst=0x40)]
			internal string szInfoTitle;
			internal int dwInfoFlags;
		}
		#endregion

		QbeSAS.ClientUI sasClientUI = null;

		NOTIFYICONDATA notifyIconTray;
		/// Text fuer das Tray Icon
		public string iconText = "";
		
		/// Erforderliche Designervariable.
		private System.ComponentModel.Container components = null;

		public TrayNotify()
		{
			//
			// Erforderlich für die Windows Form-Designerunterstützung
			//
			InitializeComponent();

			notifyIconTray = new NOTIFYICONDATA();
			sasClientUI = new QbeSAS.ClientUI(false);
		}

		/// Tray Icon entfernen
		public void DelTrayIcon()
		{
			Shell_NotifyIcon(NIM_DELETE, ref notifyIconTray);
		}

		/// Tray Icon setzen
		public void SetTrayIcon()
		{

			notifyIconTray.cbSize = System.Runtime.InteropServices.Marshal.SizeOf(notifyIconTray); // struct size
			notifyIconTray.hwnd = this.Handle; // WndProc form
			notifyIconTray.uID = uID; // message WParam, for callback
			notifyIconTray.uFlags = NIF_MESSAGE|NIF_ICON|NIF_TIP; // Flags 
			notifyIconTray.uCallbackMessage = WM_NOTIFY_TRAY; // message ID, for call back
			notifyIconTray.hIcon = this.Icon.Handle; // icon handle, for animation icon if u need
			notifyIconTray.uTimeoutAndVersion = 10 * 1000 | NOTIFYICON_VERSION; // "Balloon Tooltip" timeout

			notifyIconTray.dwInfoFlags = NIIF_INFO; // info type Flag, u can try waring, error, info

			notifyIconTray.szTip = this.Text; // tooltip message

			notifyIconTray.szInfoTitle = ""; // "Balloon Tooltip" title
			notifyIconTray.szInfo = ""; // "Balloon Tooltip" body

			Shell_NotifyIcon(NIM_ADD, ref notifyIconTray);

		}

		/// Tray icon aktualisieren
		public void UpdateTrayIcon()
		{
			notifyIconTray.uFlags = NIF_MESSAGE|NIF_ICON|NIF_TIP|NIF_INFO;
			notifyIconTray.szInfoTitle = "";
			notifyIconTray.szInfo = "";
			notifyIconTray.szTip = this.Text;
			notifyIconTray.hIcon = this.Icon.Handle;
			Shell_NotifyIcon(NIM_MODIFY, ref notifyIconTray);
		}

		/// Einen Balloon Tooltip initialisieren und anzeigen
		public void ShowTrayBalloon(string Title, string Text, int flags)
		{
			notifyIconTray.uFlags = NIF_MESSAGE|NIF_ICON|NIF_INFO|NIF_TIP;
			notifyIconTray.szInfoTitle = Title;
			notifyIconTray.szInfo = Text;
			notifyIconTray.dwInfoFlags = flags;
			notifyIconTray.hIcon = this.Icon.Handle;
			Shell_NotifyIcon(NIM_MODIFY, ref notifyIconTray);
		}

		/// Interne Routine um die Klick-Events abzufangen
		protected override void WndProc(ref Message msg) 
		{
			if (msg.Msg == WM_NOTIFY_TRAY) 
			{
				if((int)msg.LParam == WM_LBUTTONDOWN) 
				{
					sasClientUI.DisplayClientLogin();
				}
				if((int)msg.LParam == WM_RBUTTONDOWN) 
				{
					sasClientUI.DisplaySASLogin();
				}
			}
			base.WndProc(ref msg);
		}

		/// <summary>
		/// Die verwendeten Ressourcen bereinigen.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				DelTrayIcon();
				if(components != null)
				{
					components.Dispose();
				}
			}
			base.Dispose( disposing );
		}

		#region Vom Windows Form-Designer generierter Code
		/// <summary>
		/// Erforderliche Methode für die Designerunterstützung. 
		/// Der Inhalt der Methode darf nicht mit dem Code-Editor geändert werden.
		/// </summary>
		private void InitializeComponent()
		{
			// 
			// TrayNotify
			// 
			this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
			this.ClientSize = new System.Drawing.Size(144, 16);
			this.ControlBox = false;
			this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None;
			this.MaximizeBox = false;
			this.MinimizeBox = false;
			this.Name = "TrayNotify";
			this.ShowInTaskbar = false;
			this.Text = "TrayNotify";

		}
		#endregion
	}
}
