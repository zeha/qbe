using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;

namespace QbeSAS
{
	/// <summary>
	/// Zusammenfassung für IEViewer.
	/// </summary>
	public class IEViewer : System.Windows.Forms.Form
	{
		public AxSHDocVw.AxWebBrowser axWebBrowser;
		/// <summary>
		/// Erforderliche Designervariable.
		/// </summary>
		private System.ComponentModel.Container components = null;

		public IEViewer()
		{
			//
			// Erforderlich für die Windows Form-Designerunterstützung
			//
			InitializeComponent();
		}

		/// <summary>
		/// Die verwendeten Ressourcen bereinigen.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
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
			System.Resources.ResourceManager resources = new System.Resources.ResourceManager("IEViewer",System.Reflection.Assembly.GetExecutingAssembly());
			this.axWebBrowser = new AxSHDocVw.AxWebBrowser();
			((System.ComponentModel.ISupportInitialize)(this.axWebBrowser)).BeginInit();
			this.SuspendLayout();
			// 
			// axWebBrowser
			// 
			this.axWebBrowser.Dock = System.Windows.Forms.DockStyle.Fill;
			this.axWebBrowser.Enabled = true;
			this.axWebBrowser.Location = new System.Drawing.Point(0, 0);
			this.axWebBrowser.OcxState = ((System.Windows.Forms.AxHost.State)(resources.GetObject("axWebBrowser.OcxState")));
			this.axWebBrowser.Size = new System.Drawing.Size(704, 469);
			this.axWebBrowser.TabIndex = 0;
			// 
			// IEViewer
			// 
			this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
			this.ClientSize = new System.Drawing.Size(704, 469);
			this.Controls.Add(this.axWebBrowser);
			this.Name = "IEViewer";
			this.Text = "XML Viewer";
			((System.ComponentModel.ISupportInitialize)(this.axWebBrowser)).EndInit();
			this.ResumeLayout(false);

		}
		#endregion
	}
}
