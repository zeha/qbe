using System;
using System.IO;
using System.Reflection;
using System.Runtime.Remoting;
using System.Runtime.InteropServices;
using System.Security.Policy;
using System.Text;

namespace ComReg
{
	[ComImport, InterfaceType(ComInterfaceType.InterfaceIsIUnknown), Guid("e707dcde-d1cd-11d2-bab9-00c04f8eceae")]
	internal interface IAssemblyCache
	{
	    [PreserveSig()]
	    int UninstallAssembly(uint dwFlags, [MarshalAs(UnmanagedType.LPWStr)] string pszAssemblyName, IntPtr pvReserved, out uint pulDisposition);
	    [PreserveSig()]
	    int QueryAssemblyInfo(uint dwFlags, [MarshalAs(UnmanagedType.LPWStr)] string pszAssemblyName, IntPtr pAsmInfo);
	    [PreserveSig()]
	    int CreateAssemblyCacheItem(uint dwFlags, IntPtr pvReserved, out /*IAssemblyCacheItem*/IntPtr ppAsmItem, [MarshalAs(UnmanagedType.LPWStr)] String pszAssemblyName);
	    [PreserveSig()]
	    int CreateAssemblyScavenger(out object ppAsmScavenger);
	    [PreserveSig()]
	    int InstallAssembly(uint dwFlags, [MarshalAs(UnmanagedType.LPWStr)] string pszManifestFilePath, IntPtr pvReserved);
	}// IAssemblyCache

	internal class	FusionHelper : MarshalByRefObject
	{
		internal	string FullAssemblyName(string assemblyfile)
		{
			return Assembly.LoadFrom(assemblyfile).FullName;
		}
	}

	public	class FusionInstall
	{
		private const int MAX_PATH = 260;
		[DllImport("kernel32.dll", SetLastError=true)]
		static extern int SearchPath(String path, String fileName, String extension, int numBufferChars, StringBuilder buffer, int[] filePart);

		static private string whereis(string filename)
		{
			// Call SearchPath to find the full path of the file to load.
			StringBuilder sb = new StringBuilder(MAX_PATH + 1);
			if (SearchPath(null, filename, null, sb.Capacity + 1, sb, null) == 0)
			{
				throw new ApplicationException("File not found: " + filename);
			}
			return sb.ToString();
		}

		static public	string FullAssemblyName(string assemblyfile)
		{
			string reply = "";
			string adname = "FusionInstall: " + Guid.NewGuid().ToString();
			string filepath = Path.GetDirectoryName(Path.GetFullPath(assemblyfile));
			string filename = Path.GetFullPath(assemblyfile);

			Evidence si = null;
            AppDomain ad = AppDomain.CreateDomain(adname, si);
            if (ad == null)
                throw new ApplicationException("Unable to create AppDomain for assembly cache install ");
			FusionHelper r = (FusionHelper)ad.CreateInstanceAndUnwrap(Assembly.GetAssembly(typeof(ComReg.FusionHelper)).FullName, typeof(ComReg.FusionHelper).FullName);
			reply = r.FullAssemblyName(filename);
			AppDomain.Unload(ad);
			return reply;
		}


		static internal int	AddAssemblyToCache(string assembly)
		{
        IAssemblyCache ac = null;
        int hr = CreateAssemblyCache(out ac, 0);
        if (hr != 0)
        	return hr;
		else
        	return ac.InstallAssembly(0, assembly, (IntPtr)0);
		}

		static internal int	RemoveAssemblyFromCache(string assembly)
		{
	        IAssemblyCache ac = null;
	        uint n;
	        int hr = CreateAssemblyCache(out ac, 0);
	        if (hr != 0)
	        	return hr;
			else
		        return ac.UninstallAssembly(0, assembly, (IntPtr)0, out n);
		}

	    [DllImport("Fusion.dll", CharSet=CharSet.Auto)]
	    internal static extern int CreateAssemblyCache(out IAssemblyCache ppAsmCache, uint dwReserved);
	}
}