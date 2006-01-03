using System;

namespace QbeSAS
{
	/// Interface fuer das Setup um den ganzen INF Stuff
	public class WinInf
	{
		/// \brief Abstraktion um die alten INF Dateien vom Qbe Setup zu suchen und zu loeschen
		/// Sucht und loescht alte QbeSetup INF Dateien.
		/// Schreibt eine Log Datei nach system32\\Qbe\\Setup\\PFCLog.txt. Die OEM*.INF Dateien werden in system32\\..\\inf gesucht.
		/// Als Identifikation wird der Text "; $Id: netQbe.inf" in den ersten zehn Zeilen gesucht.
		public static bool delOldOemInfFiles()
		{
			try
			{
				System.IO.FileStream logStream = new System.IO.FileStream(Environment.SystemDirectory+"\\Qbe\\Setup\\PFCLog.txt",System.IO.FileMode.Append);
				System.IO.StreamWriter logfile = new System.IO.StreamWriter(logStream);
				System.IO.DirectoryInfo di = new System.IO.DirectoryInfo(Environment.SystemDirectory+"\\..\\inf");
				foreach (System.IO.FileInfo file in di.GetFiles("oem*.inf"))
				{
					try 
					{
						logfile.WriteLine("File: "+file.Name+"\n");
						System.IO.FileStream readStream = file.OpenRead();
						System.IO.StreamReader readBuff = new System.IO.StreamReader(readStream);

						bool bKillFile = false;

						for (int i=0;i<10;i++)
						{
							String thisLine = readBuff.ReadLine();
							if (thisLine.StartsWith("; $Id: netQbe.inf"))
							{
								logfile.WriteLine("  Found signature: "+thisLine+"\n");
								bKillFile = true;
								break;
							}
						}

						readBuff.Close();
						readStream.Close();

						// entsprechende Datei gefunden
						if (bKillFile)
						{
							try 
							{
								logfile.WriteLine("  Delete.\n");
								file.Delete();
							} 
							catch (Exception ex)
							{
								ex=ex;
								logfile.WriteLine("  **Exception: "+ex.StackTrace+"\n");
							}

							// es gibt immer ein dummes PNF (Precompiled iNF) zu jedem INF.
							// wenn man es nicht loescht, bringt die ganze Sache nix.
							try 
							{
								logfile.WriteLine("  Delete PNF\n");
								System.IO.FileInfo pnfFile = new System.IO.FileInfo(file.FullName.Replace(".inf",".pnf"));
								pnfFile.Delete();
							} 
							catch (Exception ex)
							{
								ex=ex;
								logfile.WriteLine("  **Exception: "+ex.StackTrace+"\n");
							}

						}


					} 
					catch (Exception ex)
					{
						ex=ex;
						logfile.WriteLine("  **Exception: "+ex.StackTrace+"\n");
					}
				}

				logfile.Close();
				return true;
			} 
			catch (Exception ex)
			{
				ex=ex;
				System.Windows.Forms.MessageBox.Show("Exception: "+ex.Message+"\n"+ex.StackTrace );
				return false;
			}
		}
	}
}
