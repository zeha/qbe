using System;

[assembly: System.Security.Permissions.SecurityPermission( System.Security.Permissions.SecurityAction.RequestMinimum, ControlThread = true )]
namespace QbeSAS
{
	public class AllCertsAreOK : System.Net.ICertificatePolicy
	{
		public bool CheckValidationResult(System.Net.ServicePoint sp, System.Security.Cryptography.X509Certificates.X509Certificate cert, System.Net.WebRequest request, int problem)
		{
			return true;	// as long as there is a cert, it's ok for us.
		}
	}

	/// Implementiert den HTTP Dienst im QbeSvc fuer alle Platformen, inklusive der Kommunikation mit dem Qbe Authentication Server.
	public class HttpService
	{
		const String RES_NEWLINE			= "\r\n";
		const String RES_HEADER_SERVER		= "Server: QbeService/" + QbeSAS.QbeClientVersion.ClientVersion + RES_NEWLINE;
		const String RES_HEADERS			= "Connection: close" + RES_NEWLINE + "Pragma: no-cache" + RES_NEWLINE + 
												"Content-Type: text/html; charset=iso-8859-1" + RES_NEWLINE + 
												"Expires: 0" + RES_NEWLINE + RES_NEWLINE;
		const String RES_HEADER_REFRESH0	= "Refresh: 0; url={0}" + RES_NEWLINE;
		const String RES_HEADER_REFRESH5	= "Refresh: 5; url={0}" + RES_NEWLINE;
		const String RES_HTTP				= "HTTP/1.0";
		const String RES_HTTP_OK			= RES_HTTP + " 200 Ok" + RES_NEWLINE;
		const String RES_HTTP_BADREQUEST	= RES_HTTP + " 400 Bad Request" + RES_NEWLINE;
		const String RES_HTTP_NOTFOUND		= RES_HTTP + " 404 Not Found" + RES_NEWLINE;
		const String RES_HTTP_ERROR			= RES_HTTP + " 500 Internal Server Error" + RES_NEWLINE;

		const String RES_DEFAULT_OK			= RES_HTTP_OK + RES_HEADER_SERVER + RES_HEADERS;
		const String RES_DEFAULT_NOTFOUND	= RES_HTTP_NOTFOUND + RES_HEADER_SERVER + RES_HEADERS + RES_NEWLINE + "Location not found." + RES_NEWLINE;

		const String RES_HTML_FOOTER		= "<br><div style=\"bottom: 4px; left: 5px; position: absolute;\"><table border=0><tr><td><span style=\"color: white; font-size: 25pt; font-weight: bold;\">Q</span></td><td><span>" + RES_NEWLINE + 
												"Qbe SAS Client " + QbeSAS.QbeClientVersion.ClientVersion + " (unofficial) &copy; 2001-2005 Christian Hofst&auml;dtler - still alive!<br>" + 
												QbeSAS.QbeClientVersion.CVSID + "</span></td></tr></table>" + 
												"</body></html>" + RES_NEWLINE + RES_NEWLINE;

		const String RES_USERAGENT			= "QbeService/" + QbeSAS.QbeClientVersion.ClientVersion;

		private System.Globalization.CultureInfo CultureFromCaller;
		private System.Globalization.CultureInfo CultureWeRequire;

		
		public class ServiceHttpResponse 
		{ 
			public String ResponseText;
			public bool ShutdownService = false;
			// more for the future

			public ServiceHttpResponse(String Text)
			{
				this.ResponseText = Text;
			}
			public bool IsRawResponse;
		}

		/// Datenspeicher fuer den HTTP Service
		class ServiceDataType
		{
			/// Aktueller Benutzername
			public String Username = "";
			/// Password des aktuellen Benutzers
			public String Password = "";
	
			/// Name des Authentication Servers. to be set by the server; if the client decides to know it better, it can change this...
			public String AuthServer = "qbe-auth";
	
			/// Internetstatus des Benutzers (kommt vom Server)
			public int User_Internet = -1;
			/// Benutzer Speicher in Prozent (kommt vom Server)
			public int User_DiskspacePercent = -1;
			public int User_DiskspaceAbsolute = -1;
			public int User_DiskspaceMaximum = -1;
			/// Verursachter Traffic in Prozent (kommt vom Server)
			public int User_TrafficPercent = -1;
			public int User_TrafficAbsolute = -1;
			public int User_TrafficMaximum = -1;

			/// Zusaetzliche IP Adresse auf die der Client reagiert.
			public System.Net.IPAddress ManagerAddress;
	
			public int ConnectionState = -1;		// server sets this
			public int ConnectionError = -1;		// server sets this

			/// Zeit der letzten Kommunikation mit dem Server
			public System.DateTime LastTime;

			/// Soll SSL verwendet werden?
			public bool bUseSSL = true;
		}

		ServiceDataType ServiceData = new ServiceDataType();
		System.Net.Sockets.TcpListener tcpListener = null;
		System.Net.Sockets.Socket tcpClient = null;
		System.Threading.Thread OwnThread = null;
		bool tcpClientRunning = false;

		class UriGetParameter
		{
			public bool IsSplitted = true;
			public String Name = "";
			public String Value = "";
			public String UnsplittedValue = "";

			public static String FindParameter(UriGetParameter[] ar, String parName)
			{
				foreach(UriGetParameter bla in ar)
					if (bla.Name.Equals(parName)) return bla.Value;
				return null;
			}
		}

		class ForeignHttpResponse
		{
			/// HTTP Status-Nummer
			public int StatusCode;
			/// HTTP Status-Beschreibung
			public string StatusDescription;
			/// Daten
			public string Body;
		}

		/// Standardkonstruktor. Erwartet eine TCP/IP Portnummer (normalerweise 7666) und ob SSL aktiviert werden soll.
		public HttpService(int tcpPort, bool enableSSL)
		{
			this.CultureFromCaller = System.Threading.Thread.CurrentThread.CurrentCulture;
			this.CultureWeRequire = new System.Globalization.CultureInfo( "en-US", false );
				
			//System.Net.IPAddress ipAddress = System.Net.Dns.Resolve("localhost").AddressList[0];
			this.ServiceData.bUseSSL = enableSSL;
			try
			{
				tcpListener = new System.Net.Sockets.TcpListener(System.Net.IPAddress.Any,tcpPort);
				Console.WriteLine("Listening on: " + tcpListener.LocalEndpoint.ToString());
			}
			catch (Exception ex)
			{	ex=ex;
				Console.WriteLine(ex.Message + "\n" + ex.StackTrace);
				tcpListener = null;
				return;
			}
		}

		/// Startet die Ausfuehrung des HTTP Service in einem eigenen Thread
		public bool RunAsOwnThread()
		{
			if (tcpListener == null)
				return false;

			try 
			{
				tcpListener.Start();
			}
			catch (Exception ex) 
			{
				ex=ex;
				return false;
			}

			OwnThread = new System.Threading.Thread(new System.Threading.ThreadStart(RunAsOwnThreadThreadFunc));
			OwnThread.Start();
			return true;
		}
		void RunAsOwnThreadThreadFunc()
		{
			this.Run2();
		}
		
		public bool KillOwnThread()
		{
			try 
			{
				OwnThread.Abort();
			} 
			catch (Exception ex) { ex=ex; /* no error */ }

			OwnThread = null;
			return true;
		}

		System.Threading.Thread SendHelloThread;
		public bool Run()
		{
			SendHelloThread = new System.Threading.Thread(new System.Threading.ThreadStart(runSendHello));
			SendHelloThread.Start();
			
			if (tcpListener == null) { Console.WriteLine("tcpListener not initialized"); return false; }

			try
			{
				tcpListener.Start();
			}
			catch (Exception ex)
			{
				ex=ex;
				Console.WriteLine("TCPListener Error: " + ex.Message + "\n" + ex.StackTrace);
				return false;
			}
			return Run2();
		}

		bool Run2()
		{
			SendHelloThread = new System.Threading.Thread(new System.Threading.ThreadStart(runSendHello));
			SendHelloThread.Start();
			while(true)
			{
				System.Threading.Thread t = new System.Threading.Thread(new System.Threading.ThreadStart(TcpClientHandlerFunc));
				tcpClient = tcpListener.AcceptSocket();

				tcpClientRunning = false;
				t.Start();
				while (!tcpClientRunning) { System.Threading.Thread.Sleep(10); }
			}
		}

		void TcpClientHandlerFunc()
		{
			System.Net.Sockets.Socket myClient = tcpClient;
			tcpClientRunning = true;		// clear lock

			// reset locale
			System.Threading.Thread.CurrentThread.CurrentCulture = this.CultureWeRequire;
			System.Threading.Thread.CurrentThread.CurrentUICulture = this.CultureWeRequire;

			System.Net.Sockets.NetworkStream stream = new System.Net.Sockets.NetworkStream(myClient,true);
			System.IO.StreamReader strr = new System.IO.StreamReader(stream);
			System.IO.StreamWriter strw = new System.IO.StreamWriter(stream);

			byte[] ipAddr = ((System.Net.IPEndPoint)tcpClient.RemoteEndPoint).Address.GetAddressBytes();
			byte[] managerIp = null; 
			if (this.ServiceData.ManagerAddress != null) { this.ServiceData.ManagerAddress.GetAddressBytes(); }
			bool ConnectionOkay = false;

			if ( (ipAddr[0] == 127) && (ipAddr[1] == 0) &&(ipAddr[2] == 0) && (ipAddr[3] == 1))
				ConnectionOkay = true;

			if ( (ipAddr[0] == 10) && (ipAddr[1] == 0) &&(ipAddr[2] == 2) && ( (ipAddr[3] == 10) || (ipAddr[3] == 20) || (ipAddr[3] == 100) ) )
				ConnectionOkay = true;

			if (managerIp != null)
			if ( (ipAddr[0] == managerIp[0]) && (ipAddr[1] == managerIp[1]) && (ipAddr[2] == managerIp[2]) && (ipAddr[3] == managerIp[3]))
				ConnectionOkay = true;

			String thisLine = strr.ReadLine();

			if (!ConnectionOkay)
			{
				strw.WriteLine("HTTP/1.0 403 Forbidden" + RES_NEWLINE + RES_NEWLINE );
				strw.Flush();
				stream.Flush();
				strr.Close();
				strw.Close();
				stream.Close();
				myClient.Close();
				return;
			}

			if (thisLine.StartsWith("GET "))
			{
				int iHttp = thisLine.LastIndexOf(" HTTP/");
				if (iHttp < 5)
					strw.WriteLine(RES_HTTP_BADREQUEST + RES_HEADERS + RES_NEWLINE + "<b>The Server could not understand your request.</b>" + RES_NEWLINE);
				else
				{
					String requestString = thisLine.Substring(4,iHttp-4);
					Uri uri = new Uri("http://localhost"+requestString);

					String requestFilename = System.Web.HttpUtility.UrlDecode(uri.AbsolutePath);
					String requestQuery = uri.Query;
					if (requestQuery.StartsWith("?")) { requestQuery = requestQuery.Substring(1); }


					Console.WriteLine("REQ: "+requestString);
						
					String[] requestParams;
					if (requestQuery != "") 
					{ 
						requestParams = requestQuery.Split("&".ToCharArray());
					} 
					else 
					{
						requestParams = new String[0];
					}

					System.Collections.ArrayList requestParameters = new System.Collections.ArrayList();

					foreach(String bla in requestParams)
					{
						UriGetParameter thisParm = new UriGetParameter();
						thisParm.UnsplittedValue = System.Web.HttpUtility.UrlDecode(bla);
						String[] splitted = bla.Split("=".ToCharArray());
						if (splitted.Length == 2)
						{
							thisParm.IsSplitted = true;
							thisParm.Name = System.Web.HttpUtility.UrlDecode(splitted[0]);
							thisParm.Value = System.Web.HttpUtility.UrlDecode(splitted[1]);
						} 
						else 
						{
							thisParm.IsSplitted = false;
						}
						requestParameters.Add(thisParm);
					}
					
					UriGetParameter[] Parameters = new UriGetParameter[requestParameters.Count];
					int el = 0;
					foreach(UriGetParameter thisParm in requestParameters)
					{
						Parameters[el] = thisParm;
						el++;
					}

					ServiceHttpResponse response = this.ParseRequest(requestFilename,Parameters,stream);
					if (response.IsRawResponse == false)
						strw.Write(response.ResponseText);
				}
				strw.Flush();
			} 
			else  
			{
				strw.WriteLine(RES_HTTP_BADREQUEST + RES_HEADERS + "<b>The Server could not understand your request.</b>" + RES_NEWLINE);
				strw.Flush();
			}
			stream.Flush();
			strr.Close();
			strw.Close();
			stream.Close();
			myClient.Close();
		}

		/// Genertiert eine Standard-HTTP-Fehlerantwort
		String MakeRespErrGeneric(String errorText)
		{
			return RES_HTTP_ERROR + RES_HEADER_SERVER + RES_HEADERS + "Sorry, an error has occoured: " + errorText + RES_NEWLINE;
		}
		/// Generiert eine Standard-HTTP-Fehlerantwort fuer nicht implementierte Befehle
		String MakeRespErrNotImplemented()
		{
			return RES_HTTP_ERROR + RES_HEADER_SERVER + RES_HEADERS + "Sorry, this command has not been implemented." + RES_NEWLINE;
		}
		/// Generiert eine Standard-HTTP-Erfolgsantwort
		String MakeRespOk()
		{
			return RES_HTTP_OK + RES_HEADER_SERVER + RES_HEADERS + "Ok, Sir." + RES_NEWLINE;
		}
		String MakeRespOkWithData(String Data)
		{
			return RES_HTTP_OK + RES_HEADER_SERVER + RES_HEADERS + Data + RES_NEWLINE;
		}
		String MakeRespHtmlHeader()
		{
			String ResponseText = "<html>" + 
							"<title>Qbe SAS Client</title>" + 
							"<body bgcolor=black text=white>" + RES_NEWLINE +
							"<style>body { background-color: #336699; background-image: url(http://qbe-auth/graphics/client_bg.png); background-repeat: no-repeat; }" + RES_NEWLINE + 
							"body,p,input,form { font-family: \"Trebuchet MS\",Geneva,Helvetica,Arial; font-size: 8pt; }" + RES_NEWLINE +
							"tr,td { font-family: \"Trebuchet MS\",Geneva,Helvetica,Arial; font-size: 8pt; }" + RES_NEWLINE +
							"a { color: white; font-weight: bold; }</style>" + RES_NEWLINE +
							"<a href=\"/web/menu\">Status</a> &nbsp; ";
			if (this.ServiceData.ConnectionError != 200)
			{	
				ResponseText += "<a href=\"/web/login\">Anmelden</a> &nbsp;";
			} else {
				ResponseText += "<a href=\"/web/logout\">Abmelden</a> &nbsp;";
			}
#if !UNIX
			ResponseText += " <a href=\"/web/cleardata\">Daten l&ouml;schen</a>";
#endif
			ResponseText += "<br><br>" + RES_NEWLINE;
			return ResponseText;
		}
		String MakeRespLoginForm(bool bAutomatic)
		{
			String ResponseText;

			bool bCloseThis;
			bCloseThis = (bAutomatic && (this.ServiceData.ConnectionError == 200) && (this.ServiceData.ConnectionState == 0));
				
			if (bCloseThis)
			{
				ResponseText = RES_DEFAULT_OK + MakeRespHtmlHeader(); 
				ResponseText += "<script>window.close(); top.window.close();</script>" + RES_NEWLINE;
				ResponseText += RES_HTML_FOOTER;
				return ResponseText;
			}
				
			ResponseText = RES_DEFAULT_OK;
			ResponseText += MakeRespHtmlHeader();
			ResponseText += "<form method=get action=\"/web/hta-login-post\" id=\"loginform\" name=\"loginform\">";
			ResponseText += "<table border=0 cellpadding=0 cellspacing=0>";

			ResponseText += "<tr><td><b>Benutzername: &nbsp;&nbsp;</b></td><td align=left><input type=text name=user id=user size=15></td></tr>";
			ResponseText += "<tr><td><b>Kennwort:</b></td><td align=left><input type=password name=pass id=pass size=15></td></tr>";

			ResponseText += "<tr><td></td><td align=left>&nbsp; <br></td></tr>";

#if !UNIX
			ResponseText += "<tr><td></td><td align=left><input type=checkbox name=save id=save> Kennwort speichern</td></tr>";
#endif
			if (bAutomatic == true)
			{
				ResponseText += "<input type=hidden name=auto value=1>";
			}
			ResponseText += "<tr><td></td><td align=left><button type=submit onClick=\"submitform();\">Anmelden</button></td></tr>";

			ResponseText += "</table>";
			ResponseText += "</form>";

#if !UNIX
			// 31337 script for saving username and password using COM in the user reg
			ResponseText += "<script language=vbscript>" + RES_NEWLINE;
			ResponseText += "Function submitform(): set iluser = CreateObject(\"QbeSAS.DataStore\"): iluser.saveData loginform.user.value,loginform.pass.value,loginform.save.checked: loginform.submit: End Function" + RES_NEWLINE;
			ResponseText += "set iluser = CreateObject(\"QbeSAS.DataStore\"): loginform.user.value = iluser.getUsername(): loginform.pass.value = iluser.getPassword(): if loginform.pass.value <> \"\" then: loginform.submit: end if" + RES_NEWLINE + "</script>" + RES_NEWLINE;
#endif

			ResponseText += RES_HTML_FOOTER;
			return ResponseText;
		}

		/// Bearbeited die schon zerpflueckte Anfrage an den HTTP Service
		ServiceHttpResponse ParseRequest(String Filename, UriGetParameter[] Parameters, System.Net.Sockets.NetworkStream rawStream)
		{
			ServiceHttpResponse resp = new ServiceHttpResponse("");
			String ResponseText = "";
			resp.IsRawResponse = false;

			switch (Filename)
			{
				case "/":
				{
					ResponseText = RES_HTTP + " 302 Moved" + RES_NEWLINE; 
					ResponseText += "Location: /web/html-frameset";
					ResponseText += RES_NEWLINE + RES_NEWLINE;

					break;
				}
				case "/web/menu":
				{
					ResponseText = RES_DEFAULT_OK;
					ResponseText += MakeRespHtmlHeader();

					// ok we talk to the user, so we have to revert to the original culture.
					System.Threading.Thread.CurrentThread.CurrentCulture = this.CultureFromCaller;
					System.Threading.Thread.CurrentThread.CurrentUICulture = this.CultureFromCaller;
					
					if (this.ServiceData.LastTime.Ticks != 0)
					{
						ResponseText += QbeSAS.SysState.getStateString(this.ServiceData.ConnectionState,this.ServiceData.User_Internet,this.ServiceData.Username) + " (" + this.ServiceData.ConnectionError.ToString() + ")<br>Letze Verbindung: " + this.ServiceData.LastTime.ToString() + RES_NEWLINE;
					}
					
					ResponseText += "<br><div style=\"position: absolute; top: 155px; left: 0px;\">" + RES_NEWLINE;
					// else: never tried!

					// part 1
					if (this.ServiceData.User_TrafficAbsolute != -1)
						ResponseText += "<div style=\"position: absolute; left: 10px;\">Traffic: " + this.ServiceData.User_TrafficAbsolute.ToString() + "MB</div>";
					
					if (this.ServiceData.User_DiskspaceAbsolute != -1)
						ResponseText += "<div style=\"position: absolute; left: 150px;\">Diskspace: " + this.ServiceData.User_DiskspaceAbsolute.ToString() + "MB</div>";
				  
				   	ResponseText += "<br>" + RES_NEWLINE;	

					// part 2
					Int32 size;
					if (this.ServiceData.User_TrafficPercent != -1)
					{
						size	= ((this.ServiceData.User_TrafficPercent*100)/120);
						if (size>120) size=120;
						ResponseText += "<div style=\"background-color: red; position: absolute; width: " + size.ToString() + "px; height: 8px; overflow: hidden; left: 10px;\">&nbsp;</div>" + RES_NEWLINE;
					}
					
					if (this.ServiceData.User_DiskspacePercent != -1)
					{
						size	= ((this.ServiceData.User_DiskspacePercent*100)/120);
						if (size>120) size=120;
						ResponseText += "<div style=\"background-color: green; position: absolute; width: " + size.ToString() + "px; height: 8px; overflow: hidden; left: 150px;\">&nbsp;</div>" + RES_NEWLINE;
					}
				   	ResponseText += "</div>";
					ResponseText += RES_HTML_FOOTER;

					System.Threading.Thread.CurrentThread.CurrentCulture = this.CultureWeRequire;
					System.Threading.Thread.CurrentThread.CurrentUICulture = this.CultureWeRequire;
					break;
				}
				case "/web/html-topframe":
					ResponseText = RES_DEFAULT_OK + "<html><body bgcolor=\"#336699\" style=\"color: white; font-family: 'Trebuchet MS',Geneva,Helvetica,Arial; font-size: 22pt; font-weight: bold;\">Qbe <span style=\"color: red;\">SAS Client "+ QbeSAS.QbeClientVersion.ClientVersion +"</span></body></html>" + RES_NEWLINE;
					break;
				case "/web/html-frameset":
					ResponseText = RES_DEFAULT_OK + "<frameset rows=\"55,*\" border=0><frame src=\"/web/html-topframe\"><frame src=\"/web/menu\"></frameset>" + RES_NEWLINE;
					break;
				case "/web/login":
					ResponseText = this.MakeRespLoginForm(false);
					break;
				case "/web/hta-login":
					ResponseText = this.MakeRespLoginForm(true);
					break;
				case "/web/hta-login-post":
				{
					if (Parameters.Length >= 2)
					{	
						bool bAutomatic = false;
						if (UriGetParameter.FindParameter(Parameters,"auto") != null) 
							if (UriGetParameter.FindParameter(Parameters,"auto") == "1")
								bAutomatic = true;

						this.ServiceData.Username = UriGetParameter.FindParameter(Parameters,"user");
						this.ServiceData.Password = UriGetParameter.FindParameter(Parameters,"pass");
						if ( (this.ServiceData.Username != null) && (this.ServiceData.Username == "") )
							this.ServiceData.Username = null;
						if ( (this.ServiceData.Password != null) && (this.ServiceData.Password == "") )
							this.ServiceData.Password = null;
			
						if ( (this.ServiceData.Username != null) && (this.ServiceData.Password != null))
						{
							ResponseText = RES_DEFAULT_OK + MakeRespHtmlHeader() + "Sie werden angemeldet... <meta http-equiv=refresh content=\"2; url=/web/hta-login-done?auto=" + (bAutomatic ? "1" : "0") + "\">" + RES_HTML_FOOTER;
							this.execLogin();
						}
						else 
						{
							ResponseText = RES_DEFAULT_OK + MakeRespHtmlHeader() + "<script language=JavaScript>alert(\"Kein Benutzername/Passwort angegeben!\"); history.go(-1);</script>" + RES_HTML_FOOTER;
						}
					}
					break;
				}
				case "/web/hta-login-done":
				{
					bool bAutomatic = false;
					if (UriGetParameter.FindParameter(Parameters,"auto") != null) 
						if (UriGetParameter.FindParameter(Parameters,"auto") == "1")
							bAutomatic = true;

					ResponseText = RES_DEFAULT_OK + MakeRespHtmlHeader();
					switch (this.ServiceData.ConnectionState)
					{
						case -1:
							if (this.ServiceData.LastTime.Ticks != 0)
							{
								ResponseText += "Anmeldung l&auml;uft. <meta http-equiv=refresh content=\"1\">";
							} 
							else 
							{
								ResponseText += "<meta http-equiv=refresh content=\"0; url=/web/hta-login\">";
							}
							break;
						case 0:
							ResponseText += "<script language=vbscript>set iluser = CreateObject(\"QbeSAS.DataStore\"): iluser.NetworkLogon \"" + this.ServiceData.Username + "\", \"" + this.ServiceData.Password + "\"" + RES_NEWLINE;

							if (bAutomatic)
								ResponseText += "window.close: window.top.close: top.window.close</script>";
							else
								ResponseText += "</script><big>Okay.</big><meta http-equiv=refresh content=\"0; url=/web/menu\">";
							break;
						case 1:
							ResponseText += "<br><br><big style=\"color: red;\">Fehler:</span><br>";
							if (this.ServiceData.ConnectionError == 401)
								ResponseText += " &nbsp; <b>Ihr Account wurde noch nicht aktiviert!</b><br>";
							if (this.ServiceData.ConnectionError == 403)
								ResponseText += " &nbsp; <b>Benutzername oder Passwort falsch.</b><br>";
							if (this.ServiceData.ConnectionError == 404)
								ResponseText += " &nbsp; <b>Dieser Client ist veraltet.</b><br>";
							if (this.ServiceData.ConnectionError == 412)
								ResponseText += " &nbsp; <b>Eine Vorraussetzung ist nicht erf&uuml;llt.</b><br>";
							if (this.ServiceData.ConnectionError == 500)
								ResponseText += " &nbsp; <b>Interner Serverfehler.</b><br>";
							break;
						default:
							break;
					}

					ResponseText += RES_HTML_FOOTER;
					break;
				}
				case "/web/logout":
				{
					ResponseText = RES_DEFAULT_OK;
					ResponseText += MakeRespHtmlHeader();
					ResponseText += "Sie werden abgemeldet...";
					ResponseText += "<script language=vbscript>set iluser = CreateObject(\"QbeSAS.DataStore\"): iluser.NetworkLogout \"" + this.ServiceData.Username + "\":</script>\n";
					ResponseText += "<meta http-equiv=refresh content=\"5; url=/web/menu\">";
					ResponseText += RES_HTML_FOOTER;
					this.execLogout();
					break;
				}
				case "/web/cleardata":
				{
					ResponseText = RES_DEFAULT_OK;
					ResponseText += MakeRespHtmlHeader();
					ResponseText += "<script language=vbscript>set iluser = CreateObject(\"QbeSAS.DataStore\"): iluser.saveData \"\",\"\",0: alert \"Benutzername und Passwort wurde entfernt.\": window.location = \"/web/menu\"</script>\n";
					ResponseText += RES_HTML_FOOTER;
					break;
				}
				case "/auth/login":
					if ( (this.ServiceData.Username == "") || (this.ServiceData.Password == ""))
					{
						ResponseText = this.MakeRespErrGeneric("No Username.");
					} 
					else 
					{
						ResponseText = this.MakeRespOk();
						this.runLogin();
					}
					break;
				case "/auth/setauthserver":
					if (Parameters.Length > 0)
					{
						this.ServiceData.AuthServer = Parameters[0].UnsplittedValue;
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Server specified!");
					}
					break;
				case "/auth/setlogin":
					if (Parameters.Length >= 2)
					{
						this.ServiceData.Username = UriGetParameter.FindParameter(Parameters,"user");
						this.ServiceData.Password = UriGetParameter.FindParameter(Parameters,"pass");

						if ( (this.ServiceData.Username != null) && (this.ServiceData.Password != null))
						{
							ResponseText = this.MakeRespOk();
						}
						else 
						{
							ResponseText = this.MakeRespErrGeneric("No Username/Password specified");
						}
					}
					break;
				case "/auth/logout":
					ResponseText = this.MakeRespOk();
					this.runLogout();
					break;
				case "/ilogin/forcerefresh":
					ResponseText = this.MakeRespOk();
					this.ServiceData.LastTime = DateTime.Now;
					break;
				case "/ilogin/statusupdate":

					switch (UriGetParameter.FindParameter(Parameters,"event"))
					{
						case "dataupdate":
						{
							String pValue;
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"internet")) != null)
								if (pValue!="")
									this.ServiceData.User_Internet = (Int32)Double.Parse(pValue);
							
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"diskspace_abs")) != null)
								if (pValue!="")
									this.ServiceData.User_DiskspaceAbsolute = (Int32)Double.Parse(pValue);
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"diskspace_max")) != null)
								if (pValue!="")
									this.ServiceData.User_DiskspaceMaximum = (Int32)Double.Parse(pValue);
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"diskspace")) != null)
								if (pValue!="")
									this.ServiceData.User_DiskspacePercent = (Int32)Double.Parse(pValue);
							
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"traffic_abs")) != null)
								if (pValue!="")
									this.ServiceData.User_TrafficAbsolute = (Int32)Double.Parse(pValue);
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"traffic_max")) != null)
								if (pValue!="")
									this.ServiceData.User_TrafficMaximum = (Int32)Double.Parse(pValue);
							
							if ( (pValue = UriGetParameter.FindParameter(Parameters,"traffic")) != null)
								if (pValue!="")
									this.ServiceData.User_TrafficPercent = (Int32)Double.Parse(pValue);

/*							if (UriGetParameter.FindParameter(Parameters,"traffic")!= null)
								if (UriGetParameter.FindParameter(Parameters,"traffic")!="")
									this.ServiceData.User_TrafficPercent = (Int32)Double.Parse(UriGetParameter.FindParameter(Parameters,"traffic"));
							 */
							ResponseText = this.MakeRespOk();
#if UNIX
							Console.WriteLine("New Data: Inet: " + this.ServiceData.User_Internet + ", Diskspace: " + this.ServiceData.User_DiskspacePercent + "%, Traffic: " + this.ServiceData.User_TrafficPercent + "%");
#endif
							break;
						}
						case "unlock":
							this.ServiceData.User_Internet = 0;
							ResponseText = this.MakeRespOk();
#if UNIX
							Console.WriteLine("Proxy unlocked you.");
#endif
							break;
						case "lock":
							this.ServiceData.User_Internet = 1;
							ResponseText = this.MakeRespOk();
#if UNIX
							Console.WriteLine("Proxy locked you.");
#endif
							break;
						default:
							ResponseText = RES_DEFAULT_NOTFOUND;
							break;
					}
					break;
				case "/system/getinfo":
					if (Parameters.Length == 1)
					{
						String WantedType = UriGetParameter.FindParameter(Parameters,"type");
						if (WantedType != null)
						{
							switch (WantedType)
							{
								case "username":
									// we have to keep it this way, the scripts on the server except it so.
									ResponseText = this.MakeRespOkWithData( (this.ServiceData.Username != null ? this.ServiceData.Username : "*VOID*" ) );
									break;
								case "username2":
									// for tracking, we add some more info
									if (this.ServiceData.LastTime.Ticks != 0)
										ResponseText = this.MakeRespOkWithData( (this.ServiceData.Username != null ? this.ServiceData.Username : "*VOID*" ) );
									else
										ResponseText = this.MakeRespOkWithData("logged out: " + (this.ServiceData.Username != null ? this.ServiceData.Username : "*VOID*" ));
									break;
								case "hostname":
									ResponseText = this.MakeRespOkWithData( Environment.MachineName );
									break;
								case "osversion":
									ResponseText = this.MakeRespOkWithData( Environment.OSVersion.ToString() );
									break;
								case "osboottime":
									ResponseText = this.MakeRespOkWithData( Environment.TickCount.ToString() );
									break;
								case "version":
									ResponseText = this.MakeRespOkWithData( QbeSAS.QbeClientVersion.ClientVersion );
									break;
								case "cvsid":
									ResponseText = this.MakeRespOkWithData( QbeSAS.QbeClientVersion.CVSID );
									break;
								case "internetstate":
									ResponseText = this.MakeRespOkWithData( this.ServiceData.User_Internet.ToString() );
									break;
								case "connectionstate":
									ResponseText = this.MakeRespOkWithData( this.ServiceData.ConnectionState.ToString() );
									break;
								case "connectionerror":
									ResponseText = this.MakeRespOkWithData( this.ServiceData.ConnectionError.ToString() );
									break;
								case "copyright":
									ResponseText = this.MakeRespOkWithData( "(C) Copyright 2001-2004 Christian Hofstaedtler" );
									break;
								case "authserver":
									ResponseText = this.MakeRespOkWithData( this.ServiceData.AuthServer );
									break;
								case "time":
									ResponseText = this.MakeRespOkWithData( DateTime.Now.Ticks.ToString() );
									break;
								default:
									ResponseText = RES_DEFAULT_NOTFOUND;
									break;
							}
						}
						else 
							ResponseText = RES_DEFAULT_NOTFOUND;
					} 
					else 
						ResponseText = RES_DEFAULT_NOTFOUND;
					break;
				case "/system/message":
					if (Parameters.Length == 1)
					{
						this.execNotification(Parameters[0].UnsplittedValue);
						ResponseText = this.MakeRespOk();
					} 
					else 
					{
						ResponseText = this.MakeRespErrGeneric("No message specified.");
					}
					break;
				case "/system/stopservice":
					ResponseText = this.MakeRespOk();
					resp.ShutdownService = true;
					break;
				case "/system/setauthserver":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						this.ServiceData.AuthServer = Parameters[0].UnsplittedValue;
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/setmanager":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						this.ServiceData.ManagerAddress = System.Net.IPAddress.Parse(Parameters[0].UnsplittedValue);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/exec":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						System.Diagnostics.Process.Start(Parameters[0].UnsplittedValue);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/distapp":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						execDistApp(Parameters[0].UnsplittedValue,false);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/distapplist":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						execDistAppList(Parameters[0].UnsplittedValue);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/forcedistapp":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						execDistApp(Parameters[0].UnsplittedValue,true);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
				case "/system/shutdown":
					ResponseText = this.MakeRespOk();
					QbeSAS.WinServiceAPI.ShutdownWindows();
					break;
				case "/system/restart":
					ResponseText = this.MakeRespOk();
					QbeSAS.WinServiceAPI.RestartWindows();
					break;
				case "/system/time":
					try 
					{
						// get time
						System.DateTime newTime = (new DateTime(1970,1,1,0,0,0)).AddSeconds( long.Parse(Parameters[0].UnsplittedValue) );
						QbeSAS.WinTimeAPI.SetTime(newTime);
					} 
					catch (Exception ex)
					{
						ex=ex;
					}
					break;
#if !UNIX
				case "/system/capturescreen":
				{
					resp.IsRawResponse = true;
					System.IO.MemoryStream stream = new System.IO.MemoryStream(200000);
					CaptureScreen.SaveDesktopImage(stream,System.Drawing.Imaging.ImageFormat.Gif);
					ResponseText = RES_HTTP_OK + RES_HEADER_SERVER + "Connection: close" + RES_NEWLINE + 
						"Pragma: no-cache" + RES_NEWLINE + 
						"Content-Type: image/gif" + RES_NEWLINE + 
						"Expires: 0" + RES_NEWLINE + RES_NEWLINE;
					System.IO.StreamWriter strw = new System.IO.StreamWriter(rawStream);
					strw.Write(ResponseText);
					strw.Flush();
					byte[] buf = stream.ToArray();
					rawStream.Write(buf,0,buf.Length);

					break;
				}
				case "/system/exec-interactive":
					if ( (Parameters.Length == 1) && ( Parameters[0].UnsplittedValue != null ) )
					{
						RunInteractiveApplication.StartApplication(Parameters[0].UnsplittedValue);
						ResponseText = this.MakeRespOk();
					} else {
						ResponseText = this.MakeRespErrGeneric("No Parameters");
					}
					break;
#endif
				default:
					ResponseText = RES_DEFAULT_NOTFOUND;
					break;
			}

			resp.ResponseText = ResponseText;
			return resp;
		}

		/// Fuehrt einen RPC Aufruf am Qbe Authentication Server aus
		ForeignHttpResponse runUrl(string urlStr)
		{
			Console.WriteLine("rpc call: " + urlStr);
			
			ForeignHttpResponse ret = new ForeignHttpResponse();
			System.Uri url = null;
			System.Net.HttpWebRequest req = null;
			System.Net.HttpWebResponse resp = null;
			System.IO.Stream respStream = null;
			System.IO.StreamReader rdr = null;

			try 
			{
				url = new System.Uri((this.ServiceData.bUseSSL == true ? "https://" : "http://") + this.ServiceData.AuthServer + urlStr);
				req = (System.Net.HttpWebRequest)System.Net.HttpWebRequest.Create(url);
				req.Proxy = new System.Net.WebProxy();
				System.Net.ServicePointManager.CertificatePolicy = new QbeSAS.AllCertsAreOK();
				
				req.UserAgent = RES_USERAGENT;
				req.Headers.Add("iLogin-User",this.ServiceData.Username);
				req.Headers.Add("iLogin-Pass",this.ServiceData.Password);

				resp = (System.Net.HttpWebResponse)req.GetResponse();
				respStream = resp.GetResponseStream();
				rdr = new System.IO.StreamReader(respStream);
				ret.StatusCode = (Int16)resp.StatusCode;
				ret.StatusDescription = resp.StatusDescription;
	
				try 
				{
					// get time
					System.DateTime newTime = (new DateTime(1970,1,1,0,0,0)).AddSeconds( long.Parse(resp.Headers["iLogin-Timestamp"]) );
					QbeSAS.WinTimeAPI.SetTime(newTime);
				} 
				catch (Exception ex)
				{
					ex=ex;
				}

				ret.Body = rdr.ReadToEnd();

			} 
			catch (System.Net.WebException webEx)
			{
				System.Net.HttpWebResponse r = (System.Net.HttpWebResponse)webEx.Response;
				ret.StatusCode = (Int16)r.StatusCode;
				ret.StatusDescription = r.StatusDescription;
				ret.Body = (new System.IO.StreamReader(r.GetResponseStream())).ReadToEnd();
			}
			catch (Exception ex)
			{
				throw new Exception("Connection Error",ex);
			} 
			finally 
			{
				if (req != null) req.Abort();
				if (resp != null) resp.Close();
				if (respStream != null) respStream.Close();
				if (rdr != null) rdr.Close();
			}
			
			return ret;
		}

		/// Ruft den RPC client/logout am Authentication Server auf
		public void runLogout()
		{
			ForeignHttpResponse Response;
			Response = this.runUrl("/rpc/client/logout");
//			Console.WriteLine(Response.StatusCode.ToString() + " " + Response.StatusDescription + ": " + Response.Body);

			this.ServiceData.LastTime = DateTime.Now;
			this.ServiceData.ConnectionError = Response.StatusCode;
			this.ServiceData.ConnectionState = -1;	// no connection
			this.ServiceData.Username = "";
			if (Response.StatusCode == 200) 
			{
			       this.ServiceData.ConnectionError = -1; // wah ZAP EVERYTHING
			}
		}

		public void runSendHello()
		{
			ForeignHttpResponse Response;
			Response = this.runUrl("/rpc/client/hello?ver="+QbeSAS.QbeClientVersion.ClientVersion);
		}
		
		/// Ruft den RPC client/login am Authentication Server auf
		public void runLogin()
		{
			ForeignHttpResponse Response;
			Response = this.runUrl("/rpc/client/login?ver="+QbeSAS.QbeClientVersion.ClientVersion);
//			Console.WriteLine(Response.StatusCode.ToString() + " " + Response.StatusDescription + ": " + Response.Body);

			this.ServiceData.LastTime = DateTime.Now;
			this.ServiceData.ConnectionError = Response.StatusCode;
			if (Response.StatusCode == 200) 
			{
				// connection ok
				this.ServiceData.ConnectionState = 0; 
			} 
			else { 
				// no connection
				this.ServiceData.ConnectionState = -1; 
				if (Response.StatusCode > 400)
					this.ServiceData.ConnectionState = 1;	// server declined request
			}
		}

		void execLogin()
		{
			this.ServiceData.User_Internet = -1;
			this.ServiceData.User_TrafficPercent = -1;
			this.ServiceData.User_DiskspacePercent = -1;
			this.ServiceData.User_TrafficAbsolute = -1;
			this.ServiceData.User_DiskspaceAbsolute = -1;
			this.ServiceData.User_TrafficMaximum = -1;
			this.ServiceData.User_DiskspaceMaximum = -1;
			this.ServiceData.ConnectionError = -1;		// reset data
			this.ServiceData.ConnectionState = -1;		// first, so the refreshing will work
			this.ServiceData.LastTime = DateTime.Now;

			System.Threading.Thread t;
			t = new System.Threading.Thread(new System.Threading.ThreadStart(this.runLogin));
			t.Start();
		}
		void execLogout()
		{
			this.ServiceData.User_Internet = -1;
			this.ServiceData.User_TrafficPercent = -1;
			this.ServiceData.User_DiskspacePercent = -1;
			this.ServiceData.User_TrafficAbsolute = -1;
			this.ServiceData.User_DiskspaceAbsolute = -1;
			this.ServiceData.User_TrafficMaximum = -1;
			this.ServiceData.User_DiskspaceMaximum = -1;
			System.Threading.Thread t;
			t = new System.Threading.Thread(new System.Threading.ThreadStart(this.runLogout));
			t.Start();
		}

		void execDistApp(String ApplicationUrl, bool bForce)
		{
			System.Threading.Thread t;
			ApplicationDistributor thread = new ApplicationDistributor(ApplicationUrl);
			thread.ModeForce = bForce;
			t = new System.Threading.Thread(new System.Threading.ThreadStart(thread.downloadAndInstallApplication));
			t.Start();
		}
		void execDistAppList(String ApplicationListUrl)
		{
			System.Threading.Thread t;
			execDistAppListThread thread = new execDistAppListThread(ApplicationListUrl);
			t = new System.Threading.Thread(new System.Threading.ThreadStart(thread.execDistAppListThreadFunc));
			t.Start();
		}
		class execDistAppListThread
		{
			String ListURL = "";
			public execDistAppListThread(String URL) { ListURL = URL; }
			public void execDistAppListThreadFunc()
			{
				System.Xml.XmlDocument doc = new System.Xml.XmlDocument();
				doc.Load(this.ListURL);
				System.Xml.XmlNodeList apps = doc.GetElementsByTagName("application");
				
				foreach(System.Xml.XmlNode app in apps)
				{
					try 
					{			
						int iValues = 0;

						String szUrl = "";
						bool bForce = false;
						for (int i=0; i < app.ChildNodes.Count; i++)
						{

							if (app.ChildNodes[i].LocalName == "url")
							{
								szUrl = app.ChildNodes[i].InnerText;
								iValues++;
							}
							if (app.ChildNodes[i].LocalName == "mode")
							{
								if (app.ChildNodes[i].InnerText == "force")
								{
									bForce = true;
								}
								iValues++;
							}
						}
						if (iValues == 2)
						{
							ApplicationDistributor appDist = new ApplicationDistributor(szUrl);
							appDist.ModeForce = bForce;
							appDist.downloadAndInstallApplication();
						}
					} 
					catch (Exception ex)
					{
						ex=ex;
					}
				}
					
			}
		}

		void execNotification(String MessageText)
		{
			System.Threading.Thread t;
			execNotificationThread thread = new execNotificationThread(MessageText);
			t = new System.Threading.Thread(new System.Threading.ThreadStart(thread.execNotificationThreadFunc));
			t.Start();
		}

		class execNotificationThread
		{
			String MessageText = "";
			public execNotificationThread(String Text) { MessageText = Text; }
			public void execNotificationThreadFunc()
			{
				QbeSAS.ServiceMessageBox.DisplayMessageBox("Qbe SAS System",MessageText);
			}
		}
	}
}
