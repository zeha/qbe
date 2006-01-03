using System;
using System.Collections;
using System.Diagnostics;

namespace QbeSAS
{
	public class SysState
	{
		/// 
		public static string runUrl(string urlString)
		{
			String returnString = "";
			System.Uri url = null;
			System.Net.WebRequest req = null;
			System.Net.WebResponse resp = null;
			System.IO.Stream respStream = null;
			System.IO.StreamReader rdr = null;

			try 
			{
				url = new System.Uri("http://127.0.0.1:7666/"+urlString);
				req = System.Net.HttpWebRequest.Create(url);
				resp = req.GetResponse();
				respStream = resp.GetResponseStream();
				rdr = new System.IO.StreamReader(respStream);
				returnString = rdr.ReadLine();
			} 
			catch (Exception ex)
			{
				returnString = "";
				throw new Exception("Connection Error",ex);
			} 
			finally 
			{
				if (req != null) req.Abort();
				if (resp != null) resp.Close();
				if (respStream != null) respStream.Close();
				if (rdr != null) rdr.Close();
			}
			return returnString;
		}

		public static string runLogout()
		{
			return runUrl("auth/logout");
		}
		public static string runLogin()
		{
			return runUrl("auth/login");
		}

		public static string queryServiceAgentEx(string question)
		{
			return runUrl("system/getinfo?type="+question);
		}

		public static string queryServiceAgentStr(string question)
		{
			try 
			{
				return queryServiceAgentEx(question);
			} 
			catch (Exception ex) { ex=ex;
				return "";
			}
		}

		public static int queryServiceAgentInt(string question)
		{	
			try 
			{
				return int.Parse(queryServiceAgentEx(question));
			} 
			catch (Exception ex){ ex=ex;
				return -1;
			}
		}

		public static string getStateString(int connectionState, int internetState, string username)
		{
			switch (connectionState)
			{
				case 0:
				{
					return "Angemeldet als "+username+".";
				}
				case 1:
				{
					return "Verbindung wurde vom Server abgelehnt.";
				}
				case -1:
				default:
				{
					return "Keine Verbindung zum Server.";
				}
			}
		}
	}
}
