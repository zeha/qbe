using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Data;

namespace QbeSAS
{
	/// <summary>
	/// Zusammenfassung für Form1.
	/// </summary>
	public class Applications : System.Windows.Forms.Form
	{
		private System.Windows.Forms.Button btnRefresh;
		private System.Windows.Forms.MenuItem menuItemLocal_GetAndViewXML;
		private System.Windows.Forms.MenuItem menuItemLocal_KillAppEntry;
		private System.Windows.Forms.TabControl tabTab;
		private System.Windows.Forms.TabPage tabLocalApps;
		private System.Windows.Forms.TabPage tabRemoteApps;
		private System.Windows.Forms.ColumnHeader hdrId;
		private System.Windows.Forms.ColumnHeader hdrVersion;
		private System.Windows.Forms.ColumnHeader hdrInstallDate;
		private System.Windows.Forms.ColumnHeader hdrUrl;
		private System.Windows.Forms.ColumnHeader hdrName;
		private System.Windows.Forms.ColumnHeader columnHeader1;
		private System.Windows.Forms.ColumnHeader hdrDate;
		private System.Windows.Forms.ListView lstAppsLocal;
		private System.Windows.Forms.ListView lstAppsRemote;
		private System.Windows.Forms.ContextMenu menuRemote;
		private System.Windows.Forms.MenuItem menuItemRemote_InstallApplication;
		private System.Windows.Forms.MenuItem menuItemRemote_InstallApplicationForce;
		private System.Windows.Forms.MenuItem menuItemRemote_CopyUrl;
		private System.Windows.Forms.ContextMenu menuLocal;
		private System.Windows.Forms.MenuItem menuItemRemote_GetAndViewXML;
		private System.Windows.Forms.Label lblStatus;
		private System.Windows.Forms.MenuItem menuItemRemote_DistApp;
		/// <summary>
		/// Erforderliche Designervariable.
		/// </summary>
		private System.ComponentModel.Container components = null;

		private void InitListLocal()
		{
			lstAppsLocal.Items.Clear();
			Microsoft.Win32.RegistryKey baseKey = Microsoft.Win32.Registry.LocalMachine.CreateSubKey("SOFTWARE\\Qbe\\Applications");
			string[] keyNames = baseKey.GetSubKeyNames();
			foreach(string keyName in keyNames)
			{
				try 
				{

					Microsoft.Win32.RegistryKey subKey = baseKey.OpenSubKey(keyName);
					System.Windows.Forms.ListViewItem item = new ListViewItem(keyName);
					item.SubItems.Add(subKey.GetValue("Version").ToString());
					item.SubItems.Add(subKey.GetValue("InstallDate").ToString());
					item.SubItems.Add(subKey.GetValue("URL").ToString());
					item.SubItems.Add(subKey.GetValue("DisplayName").ToString());
					lstAppsLocal.Items.Add(item);
				} 
				catch (Exception ex)
				{
					ex=ex;
				}
			}
		}
		private void InitListRemote()
		{
			lstAppsRemote.Items.Clear();
			System.Xml.XmlDocument doc = new System.Xml.XmlDocument();
			doc.Load("http://qbe-auth/rpc/client/apps-list");
			System.Xml.XmlNodeList apps = doc.GetElementsByTagName("application");
			
			foreach(System.Xml.XmlNode app in apps)
			{
				try 
				{			
					int iValues = 0;

					System.Windows.Forms.ListViewItem item = new ListViewItem();;
					String szUrl = "";
					String szDate = "";
					for (int i=0; i < app.ChildNodes.Count; i++)
					{

						if (app.ChildNodes[i].LocalName == "url")
						{
							szUrl = app.ChildNodes[i].InnerText;
							iValues++;
						}
						if (app.ChildNodes[i].LocalName == "date")
						{
							szDate = app.ChildNodes[i].InnerText;
							iValues++;
						}
					}
					if (iValues == 2)
					{
						item.Text = szUrl;
						item.SubItems.Add(szDate);
						lstAppsRemote.Items.Add(item);
					}
				} 
				catch (Exception ex)
				{
					ex=ex;
				}
			}
		}

		public Applications()
		{
			//
			// Erforderlich für die Windows Form-Designerunterstützung
			//
			InitializeComponent();

			this.RefreshLists();
		}

		/// <summary>
		/// Die verwendeten Ressourcen bereinigen.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				if (components != null) 
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
			this.menuLocal = new System.Windows.Forms.ContextMenu();
			this.menuItemLocal_GetAndViewXML = new System.Windows.Forms.MenuItem();
			this.menuItemLocal_KillAppEntry = new System.Windows.Forms.MenuItem();
			this.btnRefresh = new System.Windows.Forms.Button();
			this.tabTab = new System.Windows.Forms.TabControl();
			this.tabLocalApps = new System.Windows.Forms.TabPage();
			this.lstAppsLocal = new System.Windows.Forms.ListView();
			this.hdrId = new System.Windows.Forms.ColumnHeader();
			this.hdrVersion = new System.Windows.Forms.ColumnHeader();
			this.hdrInstallDate = new System.Windows.Forms.ColumnHeader();
			this.hdrUrl = new System.Windows.Forms.ColumnHeader();
			this.hdrName = new System.Windows.Forms.ColumnHeader();
			this.tabRemoteApps = new System.Windows.Forms.TabPage();
			this.lstAppsRemote = new System.Windows.Forms.ListView();
			this.columnHeader1 = new System.Windows.Forms.ColumnHeader();
			this.hdrDate = new System.Windows.Forms.ColumnHeader();
			this.menuRemote = new System.Windows.Forms.ContextMenu();
			this.menuItemRemote_GetAndViewXML = new System.Windows.Forms.MenuItem();
			this.menuItemRemote_InstallApplication = new System.Windows.Forms.MenuItem();
			this.menuItemRemote_InstallApplicationForce = new System.Windows.Forms.MenuItem();
			this.menuItemRemote_CopyUrl = new System.Windows.Forms.MenuItem();
			this.lblStatus = new System.Windows.Forms.Label();
			this.menuItemRemote_DistApp = new System.Windows.Forms.MenuItem();
			this.tabTab.SuspendLayout();
			this.tabLocalApps.SuspendLayout();
			this.tabRemoteApps.SuspendLayout();
			this.SuspendLayout();
			// 
			// menuLocal
			// 
			this.menuLocal.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
																					  this.menuItemLocal_GetAndViewXML,
																					  this.menuItemLocal_KillAppEntry});
			// 
			// menuItemLocal_GetAndViewXML
			// 
			this.menuItemLocal_GetAndViewXML.Index = 0;
			this.menuItemLocal_GetAndViewXML.Text = "&XML herunterladen und anzeigen";
			this.menuItemLocal_GetAndViewXML.Click += new System.EventHandler(this.menuItemLocal_GetAndViewXML_Click);
			// 
			// menuItemLocal_KillAppEntry
			// 
			this.menuItemLocal_KillAppEntry.Index = 1;
			this.menuItemLocal_KillAppEntry.Text = "Applikationseintrag &löschen";
			this.menuItemLocal_KillAppEntry.Click += new System.EventHandler(this.menuItemLocal_KillAppEntry_Click);
			// 
			// btnRefresh
			// 
			this.btnRefresh.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
			this.btnRefresh.Location = new System.Drawing.Point(720, 232);
			this.btnRefresh.Name = "btnRefresh";
			this.btnRefresh.TabIndex = 2;
			this.btnRefresh.Text = "&Refresh";
			this.btnRefresh.Click += new System.EventHandler(this.btnRefresh_Click);
			// 
			// tabTab
			// 
			this.tabTab.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
				| System.Windows.Forms.AnchorStyles.Left) 
				| System.Windows.Forms.AnchorStyles.Right)));
			this.tabTab.Controls.Add(this.tabLocalApps);
			this.tabTab.Controls.Add(this.tabRemoteApps);
			this.tabTab.Location = new System.Drawing.Point(8, 8);
			this.tabTab.Name = "tabTab";
			this.tabTab.SelectedIndex = 0;
			this.tabTab.Size = new System.Drawing.Size(784, 216);
			this.tabTab.TabIndex = 5;
			// 
			// tabLocalApps
			// 
			this.tabLocalApps.Controls.Add(this.lstAppsLocal);
			this.tabLocalApps.Location = new System.Drawing.Point(4, 22);
			this.tabLocalApps.Name = "tabLocalApps";
			this.tabLocalApps.Size = new System.Drawing.Size(776, 190);
			this.tabLocalApps.TabIndex = 0;
			this.tabLocalApps.Text = "Installierte Applikationen";
			// 
			// lstAppsLocal
			// 
			this.lstAppsLocal.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
																						   this.hdrId,
																						   this.hdrVersion,
																						   this.hdrInstallDate,
																						   this.hdrUrl,
																						   this.hdrName});
			this.lstAppsLocal.ContextMenu = this.menuLocal;
			this.lstAppsLocal.Dock = System.Windows.Forms.DockStyle.Fill;
			this.lstAppsLocal.FullRowSelect = true;
			this.lstAppsLocal.GridLines = true;
			this.lstAppsLocal.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
			this.lstAppsLocal.Location = new System.Drawing.Point(0, 0);
			this.lstAppsLocal.Name = "lstAppsLocal";
			this.lstAppsLocal.Size = new System.Drawing.Size(776, 190);
			this.lstAppsLocal.TabIndex = 2;
			this.lstAppsLocal.View = System.Windows.Forms.View.Details;
			// 
			// hdrId
			// 
			this.hdrId.Text = "Portable ID";
			this.hdrId.Width = 80;
			// 
			// hdrVersion
			// 
			this.hdrVersion.Text = "Version";
			this.hdrVersion.TextAlign = System.Windows.Forms.HorizontalAlignment.Right;
			this.hdrVersion.Width = 50;
			// 
			// hdrInstallDate
			// 
			this.hdrInstallDate.Text = "Install Date";
			this.hdrInstallDate.Width = 130;
			// 
			// hdrUrl
			// 
			this.hdrUrl.Text = "XML URI";
			this.hdrUrl.Width = 280;
			// 
			// hdrName
			// 
			this.hdrName.Text = "Name";
			this.hdrName.Width = 230;
			// 
			// tabRemoteApps
			// 
			this.tabRemoteApps.Controls.Add(this.lstAppsRemote);
			this.tabRemoteApps.Location = new System.Drawing.Point(4, 22);
			this.tabRemoteApps.Name = "tabRemoteApps";
			this.tabRemoteApps.Size = new System.Drawing.Size(776, 190);
			this.tabRemoteApps.TabIndex = 1;
			this.tabRemoteApps.Text = "Verfügbare Applikationen";
			// 
			// lstAppsRemote
			// 
			this.lstAppsRemote.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
																							this.columnHeader1,
																							this.hdrDate});
			this.lstAppsRemote.ContextMenu = this.menuRemote;
			this.lstAppsRemote.Dock = System.Windows.Forms.DockStyle.Fill;
			this.lstAppsRemote.FullRowSelect = true;
			this.lstAppsRemote.GridLines = true;
			this.lstAppsRemote.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
			this.lstAppsRemote.Location = new System.Drawing.Point(0, 0);
			this.lstAppsRemote.Name = "lstAppsRemote";
			this.lstAppsRemote.Size = new System.Drawing.Size(776, 190);
			this.lstAppsRemote.TabIndex = 2;
			this.lstAppsRemote.View = System.Windows.Forms.View.Details;
			// 
			// columnHeader1
			// 
			this.columnHeader1.Text = "XML URI";
			this.columnHeader1.Width = 300;
			// 
			// hdrDate
			// 
			this.hdrDate.Text = "Modified Date";
			this.hdrDate.Width = 150;
			// 
			// menuRemote
			// 
			this.menuRemote.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
																					   this.menuItemRemote_GetAndViewXML,
																					   this.menuItemRemote_InstallApplication,
																					   this.menuItemRemote_InstallApplicationForce,
																					   this.menuItemRemote_DistApp,
																					   this.menuItemRemote_CopyUrl});
			// 
			// menuItemRemote_GetAndViewXML
			// 
			this.menuItemRemote_GetAndViewXML.Index = 0;
			this.menuItemRemote_GetAndViewXML.Text = "&XML herunterladen und anzeigen";
			this.menuItemRemote_GetAndViewXML.Click += new System.EventHandler(this.menuItemRemote_GetAndViewXML_Click);
			// 
			// menuItemRemote_InstallApplication
			// 
			this.menuItemRemote_InstallApplication.Index = 1;
			this.menuItemRemote_InstallApplication.Text = "Applikation lokal &installieren";
			this.menuItemRemote_InstallApplication.Click += new System.EventHandler(this.menuItemRemote_InstallApplication_Click);
			// 
			// menuItemRemote_InstallApplicationForce
			// 
			this.menuItemRemote_InstallApplicationForce.Index = 2;
			this.menuItemRemote_InstallApplicationForce.Text = "Applikation lokal installieren (&force)";
			this.menuItemRemote_InstallApplicationForce.Click += new System.EventHandler(this.menuItemRemote_InstallApplicationForce_Click);
			// 
			// menuItemRemote_CopyUrl
			// 
			this.menuItemRemote_CopyUrl.Index = 4;
			this.menuItemRemote_CopyUrl.Text = "Adresse &kopieren";
			this.menuItemRemote_CopyUrl.Click += new System.EventHandler(this.menuItemRemote_CopyUrl_Click);
			// 
			// lblStatus
			// 
			this.lblStatus.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
			this.lblStatus.Location = new System.Drawing.Point(8, 232);
			this.lblStatus.Name = "lblStatus";
			this.lblStatus.Size = new System.Drawing.Size(528, 23);
			this.lblStatus.TabIndex = 6;
			// 
			// menuItemRemote_DistApp
			// 
			this.menuItemRemote_DistApp.Index = 3;
			this.menuItemRemote_DistApp.Text = "Applikation &verteilen";
			this.menuItemRemote_DistApp.Click += new System.EventHandler(this.menuItemRemote_DistApp_Click);
			// 
			// Applications
			// 
			this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
			this.ClientSize = new System.Drawing.Size(800, 261);
			this.Controls.Add(this.lblStatus);
			this.Controls.Add(this.tabTab);
			this.Controls.Add(this.btnRefresh);
			this.Name = "Applications";
			this.Text = "Qbe Application Explorer";
			this.tabTab.ResumeLayout(false);
			this.tabLocalApps.ResumeLayout(false);
			this.tabRemoteApps.ResumeLayout(false);
			this.ResumeLayout(false);

		}
		#endregion

		/// <summary>
		/// Der Haupteinstiegspunkt für die Anwendung.
		/// </summary>
		[STAThread]
		static void Main() 
		{
			Application.Run(new Applications());
		}

		private void RefreshLists()
		{
			InitListLocal();
			InitListRemote();
		}

		private void btnRefresh_Click(object sender, System.EventArgs e)
		{
			this.RefreshLists();
		}

		private void menuItemLocal_KillAppEntry_Click(object sender, System.EventArgs e)
		{
			if (lstAppsLocal.SelectedItems.Count > 0)
			{
				foreach ( System.Windows.Forms.ListViewItem item in lstAppsLocal.SelectedItems )
				{
					Microsoft.Win32.RegistryKey baseKey = Microsoft.Win32.Registry.LocalMachine.CreateSubKey("SOFTWARE\\Qbe\\Applications");
					baseKey.DeleteSubKeyTree(item.Text);
					
				}
				InitListLocal();
			}
		}

		private void menuItemLocal_GetAndViewXML_Click(object sender, System.EventArgs e)
		{
			if (lstAppsLocal.SelectedItems.Count == 1)
			{
				foreach ( System.Windows.Forms.ListViewItem item in lstAppsLocal.SelectedItems )
					this.viewXML(item.SubItems[3].Text);
			} 
			else 
			{
				MessageBox.Show("Bitte selektieren Sie ein Element.");
			}
		}

		private void menuItemRemote_GetAndViewXML_Click(object sender, System.EventArgs e)
		{
			if (lstAppsRemote.SelectedItems.Count == 1)
			{
				foreach ( System.Windows.Forms.ListViewItem item in lstAppsRemote.SelectedItems )
					this.viewXML(item.Text);
			} 
			else 
			{
				MessageBox.Show("Bitte selektieren Sie ein Element.");
			}
		}
		private void viewXML(string URL)
		{
			object x1 = new object();
			object x2 = new object();
			object x3 = new object();
			object x4 = new object();
			IEViewer ievw = new IEViewer();
			ievw.Text = "XML-Ansicht: " + URL;
			ievw.Show();
			ievw.axWebBrowser.Navigate(URL,ref x1,ref x2,ref x3,ref x4);
		}
		private void execURL(string URL)
		{
			try 
			{
				this.Refresh();
				String szUrl = URL;
				System.Net.WebClient cli = new System.Net.WebClient();
				cli.DownloadData(szUrl);
				System.Threading.Thread.Sleep(500);
			} 
			catch (Exception ex)
			{
				MessageBox.Show("Exception: \n"+ex.ToString(),"Applikation lokal installieren");
			}
		}

		private void menuItemRemote_InstallApplication_Click(object sender, System.EventArgs e)
		{
			foreach ( System.Windows.Forms.ListViewItem item in lstAppsRemote.SelectedItems )
			{
				lblStatus.Text = "Installing application " + item.Text;
				this.execURL("http://127.0.0.1:7666/system/distapp?" + item.Text);
			}
			lblStatus.Text = "Done.";
		}

		private void menuItemRemote_InstallApplicationForce_Click(object sender, System.EventArgs e)
		{
			foreach ( System.Windows.Forms.ListViewItem item in lstAppsRemote.SelectedItems )
			{
				lblStatus.Text = "Installing application [force] " + item.Text;
				this.execURL("http://127.0.0.1:7666/system/distapp?" + item.Text);
			}
			lblStatus.Text = "Done.";
		}
		private void menuItemRemote_CopyUrl_Click(object sender, System.EventArgs e)
		{
			foreach ( System.Windows.Forms.ListViewItem item in lstAppsRemote.SelectedItems )
			{
				System.Windows.Forms.Clipboard.SetDataObject(item.Text);
				break;
			}
		}

		private void menuItemRemote_DistApp_Click(object sender, System.EventArgs e)
		{
			if (lstAppsRemote.SelectedItems.Count == 1)
			{
				foreach ( System.Windows.Forms.ListViewItem item in lstAppsRemote.SelectedItems )
				{
					String sText = item.Text.Substring(item.Text.LastIndexOf("/")+1);
					sText = "http://qbe-auth/modules/client/applications?popup=2&appname=" + sText;

					object x1 = new object();
					object x2 = new object();
					object x3 = new object();
					object x4 = new object();
					IEViewer ievw = new IEViewer();
					ievw.Text = "Applikation verteilen";
					ievw.Show();
					ievw.axWebBrowser.Navigate(sText,ref x1,ref x2,ref x3,ref x4);
				}
			} 
			else 
			{
				MessageBox.Show("Bitte selektieren Sie ein Element.");
			}
		}
	}
}
