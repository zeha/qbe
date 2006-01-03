using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Data;
using System.Runtime.InteropServices;

namespace QbeSAS
{
	public class CaptureScreen
	{
		#region internal classes
		internal class PlatformInvokeUSER32
		{

			#region Class Variables
			public  const int SM_CXSCREEN=0;
			public  const int SM_CYSCREEN=1;
			#endregion		
	
			#region Class Functions
			[DllImport("user32.dll", EntryPoint="GetDesktopWindow")]
			public static extern IntPtr GetDesktopWindow();

			[DllImport("user32.dll",EntryPoint="GetDC")]
			public static extern IntPtr GetDC(IntPtr ptr);

			[DllImport("user32.dll",EntryPoint="GetSystemMetrics")]
			public static extern int GetSystemMetrics(int abc);

			[DllImport("user32.dll",EntryPoint="GetWindowDC")]
			public static extern IntPtr GetWindowDC(Int32 ptr);

			[DllImport("user32.dll",EntryPoint="ReleaseDC")]
			public static extern IntPtr ReleaseDC(IntPtr hWnd,IntPtr hDc);
	
			#endregion

			#region Public Constructor
			public PlatformInvokeUSER32()
			{
				// 
				// TODO: Add constructor logic here
				//
			}
			#endregion
		}

		internal class PlatformInvokeGDI32
		{

			#region Class Variables
			public  const int SRCCOPY = 13369376;
			#endregion

			#region Class Functions
			[DllImport("gdi32.dll",EntryPoint="DeleteDC")]
			public static extern IntPtr DeleteDC(IntPtr hDc);

			[DllImport("gdi32.dll",EntryPoint="DeleteObject")]
			public static extern IntPtr DeleteObject(IntPtr hDc);

			[DllImport("gdi32.dll",EntryPoint="BitBlt")]
			public static extern bool BitBlt(IntPtr hdcDest,int xDest,int yDest,int wDest,int hDest,IntPtr hdcSource,int xSrc,int ySrc,int RasterOp);

			[DllImport ("gdi32.dll",EntryPoint="CreateCompatibleBitmap")]
			public static extern IntPtr CreateCompatibleBitmap(IntPtr hdc,	int nWidth, int nHeight);

			[DllImport ("gdi32.dll",EntryPoint="CreateCompatibleDC")]
			public static extern IntPtr CreateCompatibleDC(IntPtr hdc);

			[DllImport ("gdi32.dll",EntryPoint="SelectObject")]
			public static extern IntPtr SelectObject(IntPtr hdc,IntPtr bmp);
			#endregion
	
			#region Public Constructor
			public PlatformInvokeGDI32()
			{
				// 
				// TODO: Add constructor logic here
				//
			}
			#endregion
	
		}
		//This structure shall be used to keep the size of the screen.
		internal struct SIZE
		{
			public int cx;
			public int cy;
		}	
		#endregion
		#region Constructor
		public CaptureScreen()
		{
		}

		#endregion
		#region Class Variable Declaration
		protected static IntPtr m_HBitmap;
		#endregion
		#region internal Class Functions
		internal static Bitmap GetDesktopImage()
		{
			//In size variable we shall keep the size of the screen.
			SIZE size;
			
			//Here we get the handle to the desktop device context.
			IntPtr 	hDC = PlatformInvokeUSER32.GetDC(PlatformInvokeUSER32.GetDesktopWindow()); 

			//Here we make a compatible device context in memory for screen device context.
			IntPtr hMemDC = PlatformInvokeGDI32.CreateCompatibleDC(hDC);
			
			//We pass SM_CXSCREEN constant to GetSystemMetrics to get the X coordinates of screen.
			size.cx = PlatformInvokeUSER32.GetSystemMetrics(PlatformInvokeUSER32.SM_CXSCREEN);

			//We pass SM_CYSCREEN constant to GetSystemMetrics to get the Y coordinates of screen.
			size.cy = PlatformInvokeUSER32.GetSystemMetrics(PlatformInvokeUSER32.SM_CYSCREEN);
			
			//We create a compatible bitmap of screen size and using screen device context.
			m_HBitmap = PlatformInvokeGDI32.CreateCompatibleBitmap(hDC, size.cx, size.cy);

			//As m_HBitmap is IntPtr we can not check it against null. For this purspose IntPtr.Zero is used.
			if (m_HBitmap!=IntPtr.Zero)
			{
				//Here we select the compatible bitmap in memeory device context and keeps the refrence to Old bitmap.
				IntPtr hOld = (IntPtr) PlatformInvokeGDI32.SelectObject(hMemDC, m_HBitmap);
				//We copy the Bitmap to the memory device context.
				PlatformInvokeGDI32.BitBlt(hMemDC, 0, 0,size.cx,size.cy, hDC, 0, 0, PlatformInvokeGDI32.SRCCOPY);
				//We select the old bitmap back to the memory device context.
				PlatformInvokeGDI32.SelectObject(hMemDC, hOld);
				//We delete the memory device context.
				PlatformInvokeGDI32.DeleteDC(hMemDC);
				//We release the screen device context.
				PlatformInvokeUSER32.ReleaseDC(PlatformInvokeUSER32.GetDesktopWindow(), hDC);
				//Image is created by Image bitmap handle and returned.
				return System.Drawing.Image.FromHbitmap(m_HBitmap); 
			}
			//If m_HBitmap is null retunrn null.
			return null;
		}
		#endregion

		public static bool SaveDesktopImage(System.IO.Stream stream, System.Drawing.Imaging.ImageFormat imageFormat)
		{
			bool NoShot = false;
			/* get shot permission from machine */
			try 
			{
				NoShot = bool.Parse((string)Microsoft.Win32.Registry.LocalMachine.OpenSubKey("SOFTWARE\\Qbe\\SAS\\Client",false).GetValue("DisableScreenshot",bool.FalseString));
			} 
			catch (Exception ex)
			{
					ex=ex;
			}
			/* get shot permission from userprofile */
			if (!NoShot)
			{
				try 
				{
					NoShot = bool.Parse((string)Microsoft.Win32.Registry.CurrentUser.OpenSubKey("SOFTWARE\\Qbe\\SAS\\Client",false).GetValue("DisableScreenshot",bool.FalseString));
				} 
				catch (Exception ex)
				{
						ex=ex;
				}
			}

			/* create image */
			Image img;
			if (!NoShot)
				img = CaptureScreen.GetDesktopImage();
			else
			{
				/* empty image if screenshot is disabled via registry */
				img = new Bitmap(500,30);
			}
			/* write out screenshot */
			img.Save(stream,imageFormat);
			return true;
		}
	}
}
