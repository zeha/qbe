using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Data;

namespace QbeSAS
{
	/// <summary>
	/// Zusammenfassung für LoginScript.
	/// </summary>
	public class LoginScript : System.Windows.Forms.Form
	{
		private System.Windows.Forms.Button btnClose;
		private System.Windows.Forms.TextBox txtOutput;
		private System.Windows.Forms.PictureBox pctBackground;
		System.String strLoginScript;
		System.String strCopiedLoginScript; // loginscript filename after copy
		System.String strHandler;
		System.String strHandlerArgs;
		System.Boolean bCopyFile;
		System.Boolean bAutoClose;
		private System.Diagnostics.ProcessStartInfo siScript;
		System.Diagnostics.Process procScript;
		private System.Threading.Thread threadScript;
		private System.Threading.Thread threadWriteStdIn;
		private System.Threading.Thread threadReadStdOut;
		private System.Threading.Thread threadReadStdErr;
		private System.Windows.Forms.Timer tmrStart;
		private System.ComponentModel.IContainer components;

		public LoginScript()
		{
			//
			// Erforderlich für die Windows Form-Designerunterstützung
			//
			InitializeComponent();
		}

		public void StartScript()
		{
			System.String strScript;

			if (this.bCopyFile)
			{
				System.String newFileName = System.IO.Path.GetTempPath() + "\\" + System.IO.Path.GetFileName(this.strLoginScript);
				try {System.IO.File.Delete(newFileName);}  
				catch (Exception ex) {ex=ex; MessageBox.Show("ex handler"); }
				System.IO.File.Copy(this.strLoginScript,newFileName);
				strScript = newFileName;
				this.strCopiedLoginScript = newFileName;
			} 
			else 
			{
				strScript = this.strLoginScript;
			}

			AddLoginScriptOutput("Qbe Login Script: " + strScript);
			AddLoginScriptOutput("AutoClose: " + this.bAutoClose);
			AddLoginScriptOutput("CopyScript: " + this.bCopyFile);

			siScript = new System.Diagnostics.ProcessStartInfo(strHandler,strHandlerArgs);
			siScript.CreateNoWindow = true;
			siScript.RedirectStandardError = true;
			siScript.RedirectStandardOutput = true;
			siScript.RedirectStandardInput = true;
			siScript.WorkingDirectory = System.IO.Path.GetPathRoot(strScript);
			siScript.UseShellExecute = false;

			procScript = new System.Diagnostics.Process();
			procScript.StartInfo = siScript;

			threadScript = new System.Threading.Thread(new System.Threading.ThreadStart(LoginScriptThread));
			threadWriteStdIn = new System.Threading.Thread(new System.Threading.ThreadStart(WriteStdInThread));
			threadReadStdOut = new System.Threading.Thread(new System.Threading.ThreadStart(ReadStdOutThread));
			threadReadStdErr = new System.Threading.Thread(new System.Threading.ThreadStart(ReadStdErrThread));

			procScript.Start();
			threadScript.Start();
			threadWriteStdIn.Start();
			threadReadStdOut.Start();
			threadReadStdErr.Start();
		}
		

		public void LoginScriptThread() 
		{
			this.procScript.WaitForExit();
			btnClose.Enabled = true;
			Application.DoEvents();

			try 
			{
				threadWriteStdIn.Abort();
				threadReadStdOut.Abort();
				threadReadStdErr.Abort();

				if (this.bCopyFile)
				{	// delete the file we created.
					try {System.IO.File.Delete(this.strCopiedLoginScript);}  
					catch (Exception ex) {ex=ex; MessageBox.Show("ex handler"); }
				}

				AddLoginScriptOutput("");
				AddLoginScriptOutput("Qbe Login Script returned Exit Code " + this.procScript.ExitCode.ToString());


				if ((this.procScript.ExitCode == 0) && (this.bAutoClose))
				{
					this.Close();
				}
			}
			catch (Exception ex)
			{
				ex=ex;
			}

		}

		void AddLoginScriptOutput(System.String output)
		{
			this.txtOutput.Text += output + "\r\n";
			this.txtOutput.Select(this.txtOutput.TextLength,0);
			this.Refresh();
			Application.DoEvents();
		}
		
		public void ReadStdOutThread() 
		{			
			string output;
			while ((output = procScript.StandardOutput.ReadLine()) != null)
			{
				AddLoginScriptOutput(output);
			}
		}
		public void ReadStdErrThread() 
		{
			string output;
			while ((output = procScript.StandardError.ReadLine()) != null)
			{
				AddLoginScriptOutput(output);
			}
		}
		public void WriteStdInThread() 
		{
		/*	while (!procScript.HasExited)
			{
				try 
				{
					procScript.WaitForInputIdle();
				} 
				catch (Exception ex)
				{
					try 
					{
						ex=ex;
						InputDialog dlg = new InputDialog();
						dlg.ShowDialog(this);
						procScript.StandardInput.WriteLine(dlg.strAnswer); 

						this.Refresh();
						Application.DoEvents();
						System.Threading.Thread.Sleep(100);
					} 
					catch (Exception ex2) { ex2=ex2; }
				}
			} 
			*/
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
			this.components = new System.ComponentModel.Container();
			System.Resources.ResourceManager resources = new System.Resources.ResourceManager("LoginScript",System.Reflection.Assembly.GetExecutingAssembly());
			this.btnClose = new System.Windows.Forms.Button();
			this.txtOutput = new System.Windows.Forms.TextBox();
			this.pctBackground = new System.Windows.Forms.PictureBox();
			this.tmrStart = new System.Windows.Forms.Timer(this.components);
			this.SuspendLayout();
			// 
			// btnClose
			// 
			this.btnClose.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left) 
				| System.Windows.Forms.AnchorStyles.Right)));
			this.btnClose.BackColor = System.Drawing.SystemColors.Control;
			this.btnClose.DialogResult = System.Windows.Forms.DialogResult.Cancel;
			this.btnClose.Enabled = false;
			this.btnClose.Location = new System.Drawing.Point(200, 384);
			this.btnClose.Name = "btnClose";
			this.btnClose.Size = new System.Drawing.Size(80, 24);
			this.btnClose.TabIndex = 0;
			this.btnClose.Text = "Close";
			this.btnClose.Click += new System.EventHandler(this.btnClose_Click);
			// 
			// txtOutput
			// 
			this.txtOutput.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
				| System.Windows.Forms.AnchorStyles.Left) 
				| System.Windows.Forms.AnchorStyles.Right)));
			this.txtOutput.AutoSize = false;
			this.txtOutput.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.txtOutput.BorderStyle = System.Windows.Forms.BorderStyle.None;
			this.txtOutput.ForeColor = System.Drawing.Color.White;
			this.txtOutput.Location = new System.Drawing.Point(8, 88);
			this.txtOutput.Multiline = true;
			this.txtOutput.Name = "txtOutput";
			this.txtOutput.ReadOnly = true;
			this.txtOutput.ScrollBars = System.Windows.Forms.ScrollBars.Both;
			this.txtOutput.Size = new System.Drawing.Size(464, 288);
			this.txtOutput.TabIndex = 1;
			this.txtOutput.Text = "";
			// 
			// pctBackground
			// 
			this.pctBackground.BackColor = System.Drawing.Color.Transparent;
			this.pctBackground.Image = ((System.Drawing.Image)(resources.GetObject("pctBackground.Image")));
			this.pctBackground.Location = new System.Drawing.Point(0, 0);
			this.pctBackground.Name = "pctBackground";
			this.pctBackground.Size = new System.Drawing.Size(400, 200);
			this.pctBackground.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pctBackground.TabIndex = 2;
			this.pctBackground.TabStop = false;
			// 
			// tmrStart
			// 
			this.tmrStart.Tick += new System.EventHandler(this.tmrStart_Tick);
			// 
			// LoginScript
			// 
			this.AcceptButton = this.btnClose;
			this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
			this.BackColor = System.Drawing.Color.FromArgb(((System.Byte)(51)), ((System.Byte)(102)), ((System.Byte)(153)));
			this.CancelButton = this.btnClose;
			this.ClientSize = new System.Drawing.Size(480, 414);
			this.ControlBox = false;
			this.Controls.Add(this.txtOutput);
			this.Controls.Add(this.btnClose);
			this.Controls.Add(this.pctBackground);
			this.MaximizeBox = false;
			this.MinimizeBox = false;
			this.Name = "LoginScript";
			this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
			this.Text = "Qbe Login Script";
			this.Load += new System.EventHandler(this.LoginScript_Load);
			this.ResumeLayout(false);

		}
		#endregion

		/// <summary>
		/// Der Haupteinstiegspunkt für die Anwendung.
		/// </summary>
		[STAThread]
		static void Main(string[] args)
		{
			if (args.Length < 4)
			{
				MessageBox.Show("Usage: QbeLoginScript.exe HandlerPath HandlerArgs LoginScriptFile [CopyLoginScript = FALSE] [AutoCloseWindow = TRUE]","Qbe SAS Client");
				return;
			}
			
			try 
			{
				LoginScript frm;
				frm = new LoginScript();
				frm.strHandler = args[0];
				frm.strHandlerArgs = args[1];
				frm.strLoginScript = args[2];
				if (args.Length > 3)
					frm.bCopyFile = System.Boolean.Parse(args[3]);
				else
					frm.bCopyFile = false;
				if (args.Length > 4)
					frm.bAutoClose = System.Boolean.Parse(args[4]);
				else
					frm.bAutoClose = true;
				Application.Run(frm);
			} 
			catch (Exception ex)
			{
				MessageBox.Show("Error: "+ ex.Message + "\r\n" +ex.StackTrace,"Qbe SAS Login",System.Windows.Forms.MessageBoxButtons.OK,System.Windows.Forms.MessageBoxIcon.Error);
			}
		}

		private void btnClose_Click(object sender, System.EventArgs e)
		{
			Application.Exit();
		}

		private void LoginScript_Load(object sender, System.EventArgs e)
		{
			this.Show();
			this.WindowState = System.Windows.Forms.FormWindowState.Normal;
			Application.DoEvents();
			tmrStart.Enabled = true;
		}

		private void tmrStart_Tick(object sender, System.EventArgs e)
		{
			this.tmrStart.Enabled = false;
			this.StartScript();
		}
	}
}
