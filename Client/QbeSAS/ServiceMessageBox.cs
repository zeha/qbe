using System;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	/// Abstraktion um das Win32 MessageBox() API, bzw. die .NET Version, für den Http Service
	public class ServiceMessageBox
	{
#if !UNIX
		[DllImport("user32.dll", EntryPoint="MessageBox")]
		protected static extern int MyWin32MessageBox(int windowHandle, string text, string caption, int options);

		public static void DisplayMessageBox(String Title, String Message)
		{
			MyWin32MessageBox(0, Message, Title, 0x00200000|0x00000040);
		}
#else
		public static void DisplayMessageBox(String Title, String Message)
		{
			Console.WriteLine("System Notification: \"" + Message +"\"");		
		}
#endif
	}
}
