//
// $Id: Splash.cs 174 2004-09-29 23:18:03Z ch $
//
// (C) Copyright 2001-2004 Christian Hofstaedtler

using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;

namespace QbeSAS
{
	/// Splash stellt das Start-Fenster fuer den Windows QbeServer zur Verfuegung
	public class Splash : System.Windows.Forms.Form
	{
		public System.Windows.Forms.Label stLbl_Service;
		public System.Windows.Forms.Label stLbl_NetInit;
		public System.Windows.Forms.Label stLbl_Client;
		private System.Windows.Forms.PictureBox pctBackground;
		public System.Windows.Forms.PictureBox pctService;
		public System.Windows.Forms.PictureBox pctNetInit;
		public System.Windows.Forms.PictureBox pctClient;
		public System.Windows.Forms.PictureBox pctTpl_Ok;
		public System.Windows.Forms.PictureBox pctTpl_Fail;
		public System.Windows.Forms.Label lblVersion;
		/// <summary>
		/// Erforderliche Designervariable.
		/// </summary>
		private System.ComponentModel.Container components = null;

		public Splash()
		{
			//
			// Erforderlich für die Windows Form-Designerunterstützung
			//
			InitializeComponent();
		}

		/// Die verwendeten Ressourcen bereinigen.
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
		/// Erforderliche Methode für die Designerunterstützung. 
		/// Der Inhalt der Methode darf nicht mit dem Code-Editor geändert werden.
		private void InitializeComponent()
		{
			System.Resources.ResourceManager resources = new System.Resources.ResourceManager("Splash",System.Reflection.Assembly.GetExecutingAssembly());
			this.pctBackground = new System.Windows.Forms.PictureBox();
			this.stLbl_Service = new System.Windows.Forms.Label();
			this.stLbl_NetInit = new System.Windows.Forms.Label();
			this.stLbl_Client = new System.Windows.Forms.Label();
			this.pctService = new System.Windows.Forms.PictureBox();
			this.pctNetInit = new System.Windows.Forms.PictureBox();
			this.pctClient = new System.Windows.Forms.PictureBox();
			this.pctTpl_Ok = new System.Windows.Forms.PictureBox();
			this.pctTpl_Fail = new System.Windows.Forms.PictureBox();
			this.lblVersion = new System.Windows.Forms.Label();
			this.SuspendLayout();
			// 
			// pctBackground
			// 
			this.pctBackground.BackColor = System.Drawing.Color.Transparent;
			this.pctBackground.Dock = System.Windows.Forms.DockStyle.Fill;
			this.pctBackground.Image = ((System.Drawing.Image)(resources.GetObject("pctBackground.Image")));
			this.pctBackground.Location = new System.Drawing.Point(0, 0);
			this.pctBackground.Name = "pctBackground";
			this.pctBackground.Size = new System.Drawing.Size(400, 200);
			this.pctBackground.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctBackground.TabIndex = 0;
			this.pctBackground.TabStop = false;
			this.pctBackground.Click += new System.EventHandler(this.pctBackground_Click);
			// 
			// stLbl_Service
			// 
			this.stLbl_Service.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.stLbl_Service.Font = new System.Drawing.Font("Trebuchet MS", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((System.Byte)(0)));
			this.stLbl_Service.ForeColor = System.Drawing.Color.White;
			this.stLbl_Service.Location = new System.Drawing.Point(128, 120);
			this.stLbl_Service.Name = "stLbl_Service";
			this.stLbl_Service.Size = new System.Drawing.Size(224, 16);
			this.stLbl_Service.TabIndex = 1;
			this.stLbl_Service.Text = "System Service";
			this.stLbl_Service.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// stLbl_NetInit
			// 
			this.stLbl_NetInit.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.stLbl_NetInit.Font = new System.Drawing.Font("Trebuchet MS", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((System.Byte)(0)));
			this.stLbl_NetInit.ForeColor = System.Drawing.Color.White;
			this.stLbl_NetInit.Location = new System.Drawing.Point(128, 104);
			this.stLbl_NetInit.Name = "stLbl_NetInit";
			this.stLbl_NetInit.Size = new System.Drawing.Size(224, 16);
			this.stLbl_NetInit.TabIndex = 2;
			this.stLbl_NetInit.Text = "Network Initialization";
			this.stLbl_NetInit.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// stLbl_Client
			// 
			this.stLbl_Client.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.stLbl_Client.Font = new System.Drawing.Font("Trebuchet MS", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((System.Byte)(0)));
			this.stLbl_Client.ForeColor = System.Drawing.Color.White;
			this.stLbl_Client.Location = new System.Drawing.Point(128, 136);
			this.stLbl_Client.Name = "stLbl_Client";
			this.stLbl_Client.Size = new System.Drawing.Size(224, 16);
			this.stLbl_Client.TabIndex = 3;
			this.stLbl_Client.Text = "Client";
			this.stLbl_Client.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// pctService
			// 
			this.pctService.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.pctService.Location = new System.Drawing.Point(112, 120);
			this.pctService.Name = "pctService";
			this.pctService.Size = new System.Drawing.Size(16, 16);
			this.pctService.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctService.TabIndex = 4;
			this.pctService.TabStop = false;
			// 
			// pctNetInit
			// 
			this.pctNetInit.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.pctNetInit.Location = new System.Drawing.Point(112, 104);
			this.pctNetInit.Name = "pctNetInit";
			this.pctNetInit.Size = new System.Drawing.Size(16, 16);
			this.pctNetInit.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctNetInit.TabIndex = 5;
			this.pctNetInit.TabStop = false;
			// 
			// pctClient
			// 
			this.pctClient.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.pctClient.Location = new System.Drawing.Point(112, 136);
			this.pctClient.Name = "pctClient";
			this.pctClient.Size = new System.Drawing.Size(16, 16);
			this.pctClient.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctClient.TabIndex = 6;
			this.pctClient.TabStop = false;
			// 
			// pctTpl_Ok
			// 
			this.pctTpl_Ok.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.pctTpl_Ok.Image = ((System.Drawing.Image)(resources.GetObject("pctTpl_Ok.Image")));
			this.pctTpl_Ok.Location = new System.Drawing.Point(16, 104);
			this.pctTpl_Ok.Name = "pctTpl_Ok";
			this.pctTpl_Ok.Size = new System.Drawing.Size(16, 16);
			this.pctTpl_Ok.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctTpl_Ok.TabIndex = 7;
			this.pctTpl_Ok.TabStop = false;
			this.pctTpl_Ok.Visible = false;
			// 
			// pctTpl_Fail
			// 
			this.pctTpl_Fail.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.pctTpl_Fail.Image = ((System.Drawing.Image)(resources.GetObject("pctTpl_Fail.Image")));
			this.pctTpl_Fail.Location = new System.Drawing.Point(40, 104);
			this.pctTpl_Fail.Name = "pctTpl_Fail";
			this.pctTpl_Fail.Size = new System.Drawing.Size(16, 16);
			this.pctTpl_Fail.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctTpl_Fail.TabIndex = 8;
			this.pctTpl_Fail.TabStop = false;
			this.pctTpl_Fail.Visible = false;
			// 
			// lblVersion
			// 
			this.lblVersion.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.lblVersion.Font = new System.Drawing.Font("Trebuchet MS", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((System.Byte)(0)));
			this.lblVersion.ForeColor = System.Drawing.Color.White;
			this.lblVersion.Location = new System.Drawing.Point(127, 68);
			this.lblVersion.Name = "lblVersion";
			this.lblVersion.Size = new System.Drawing.Size(209, 15);
			this.lblVersion.TabIndex = 9;
			this.lblVersion.Text = "[version]";
			this.lblVersion.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// Splash
			// 
			this.AutoScale = false;
			this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
			this.BackColor = System.Drawing.Color.White;
			this.ClientSize = new System.Drawing.Size(376, 160);
			this.ControlBox = false;
			this.Controls.Add(this.lblVersion);
			this.Controls.Add(this.pctTpl_Fail);
			this.Controls.Add(this.pctTpl_Ok);
			this.Controls.Add(this.pctClient);
			this.Controls.Add(this.pctNetInit);
			this.Controls.Add(this.pctService);
			this.Controls.Add(this.stLbl_Service);
			this.Controls.Add(this.stLbl_Client);
			this.Controls.Add(this.stLbl_NetInit);
			this.Controls.Add(this.pctBackground);
			this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None;
			this.MaximizeBox = false;
			this.MinimizeBox = false;
			this.Name = "Splash";
			this.ShowInTaskbar = false;
			this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
			this.ResumeLayout(false);

		}
		#endregion

		private void pctBackground_Click(object sender, System.EventArgs e)
		{
		
		}
	}
}
