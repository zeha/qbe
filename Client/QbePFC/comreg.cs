using System;
using System.Collections;
using System.Globalization;
using System.IO;
using System.Reflection;
using System.Runtime.Remoting;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Security.Policy;
using System.Text;

using Microsoft.Win32;

namespace ComReg
{

	public	class ComRegistrationClass : MarshalByRefObject
	{
		// Some privates
        private const string strManagedCategoryGuid = "{62C8FE65-4EBB-45e7-B440-6E39B2CDBF29}";

		// Marshaler clsid
        private const string strPrxyStubMarshalerGuid = "{34E6B1BA-CFD5-4ea3-B0C8-D16DF05C2C4B}";
        private const string strDocStringPrefix = "";
        private const string strManagedTypeThreadingModel = "Both";
        private const string strComponentCategorySubKey = "Component Categories";
        private const string strManagedCategoryDescription = ".NET Category";
        private const string strMsCorEEFileName = "mscoree.dll";
        private string strRuntimeVersion = RuntimeEnvironment.GetSystemVersion();

		public	class RegistryValue
		{
			public RegistryValue(string pkey, string pattr, string pvalue)
			{
				key = pkey;
				attr = pattr;
				value=pvalue;
			}

			public string key;
			public string attr;
			public object value;
		}

		public object[] GetTypes(Assembly asm)
		{
			ArrayList regtypes = new ArrayList();

            // No assembly is an error
            if (asm == null)
                throw new ArgumentNullException("assembly");

			// Fetch all of the types in each module that require registration.
			foreach (Module m in asm.GetModules())
			{
				foreach (Type t in m.GetTypes())
				{
					if ((t.IsPublic || t.IsNestedPublic) && (t.IsClass || t.IsInterface))
						regtypes.Add(t);
				}
			}
			return (object[])regtypes.ToArray();
		}

		private bool GetIClassXXXInfo(Type t, out string name, out Guid guid)
		{
			name = "";
			guid = new Guid();
			object[] attrs = t.GetCustomAttributes(typeof(ClassInterfaceAttribute), true);
			if (attrs.Length != 0 && ((ClassInterfaceAttribute)attrs[0]).Value == ClassInterfaceType.AutoDual)
			{
				// Now add IClassXXX Information
				// I need to get the ITypeInfo and munge around like a mad thing because the CLR doesn't really believe that
				// an IClassXXX exists
				IntPtr pTI = Marshal.GetITypeInfoForType(t);
				if (pTI != (IntPtr)0)
				{
					UCOMITypeInfo ti = (UCOMITypeInfo)Marshal.GetObjectForIUnknown(pTI);
					IntPtr pATTR;
					ti.GetTypeAttr(out pATTR);
					TYPEATTR attr = (TYPEATTR)Marshal.PtrToStructure(pATTR, typeof(TYPEATTR));

					if (attr.typekind == TYPEKIND.TKIND_COCLASS)
					{
						// Rest easy we got a coclass
						for(int i = 0; i < attr.cImplTypes; i++)
						{
							int href;
							int	flags;
							ti.GetImplTypeFlags(i, out flags);
							if ((flags & (int)IMPLTYPEFLAGS.IMPLTYPEFLAG_FDEFAULT) != 0  &&
								(flags & (int)IMPLTYPEFLAGS.IMPLTYPEFLAG_FSOURCE) == 0)
							{
								// This is the default interface
								ti.GetRefTypeOfImplType(i, out href);
								if (href != 0)
								{
									UCOMITypeInfo reftype;
									ti.GetRefTypeInfo(href, out reftype);

									// Retrieve guid
									IntPtr pREFATTR;
									reftype.GetTypeAttr(out pREFATTR);
									TYPEATTR refattr = (TYPEATTR)Marshal.PtrToStructure(pREFATTR, typeof(TYPEATTR));
									guid = refattr.guid;
									reftype.ReleaseTypeAttr(pREFATTR);

									// Retrieve name
									string strDocString;
									int dwHelpContext;
									string strHelpFile;

									// Should be TYPEATTR.MEMBER_ID_NIL - but there was a typo in devland
									reftype.GetDocumentation(TYPEATTR.MEMBER_ID_NIL, out name, out strDocString, out dwHelpContext, out strHelpFile);
								}
							}
						}
					}
					ti.ReleaseTypeAttr(pATTR);
				}
			}
			return guid != Guid.Empty && name != "";
		}

		private object[]	RegisterType(Type t, bool proxystubmarshaler, bool codebase, string remotehost, string runas)
		{
			object[] keys = GenerateRegKeysForType(t, proxystubmarshaler, codebase, remotehost, runas);
			return keys;
		}

		private void	WriteStream(FileStream fs, string text)
		{
	        byte[] bytes = System.Text.Encoding.UTF8.GetBytes(text);
			fs.Write(bytes, 0, bytes.Length);
		}

		private void	GenerateRegFile(ArrayList keys, string regfile)
		{
			if (regfile == null || regfile == "") regfile="default.reg";

			FileStream fs = File.Create(regfile);
			WriteStream(fs, "REGEDIT4" + Environment.NewLine);
			string lastkey = "";
			string attr = "";
			string value = "";
			foreach(RegistryValue r in keys)
			{
				if (r.key != lastkey)
				{
					lastkey = r.key;
					WriteStream(fs,  Environment.NewLine + "[" + lastkey + "]"  + Environment.NewLine);
				}

				attr = (r.attr == null || r.attr == "") ? "@" : '"' + r.attr + '"';
				value = '"' + ((r.value == null || (string)r.value == "") ? "" : r.value.ToString()) + '"';
				WriteStream(fs, attr + "=" + value + Environment.NewLine);
			}

			fs.Close();

			return;
		}

		private bool LeftCompare(string s, string target, out string remainder)
		{
			if(s.Substring(0, target.Length) == target)
			{
				remainder = s.Substring(target.Length, s.Length - target.Length);
				return true;
			}
			remainder = "";
			return false;
		}

		private RegistryKey CreateSubKey(string key)
		{
			string remainder = "";
			RegistryKey branch = null;

			if (LeftCompare(key, "HKEY_CLASSES_ROOT\\", out remainder))
			{
				branch = Registry.ClassesRoot;
			}
#if EXTRA_REG
			// I don't need these others yet - maybe I never will
			else if (LeftCompare(key, "HKEY_CURRENT_USER\\", out remainder))
			{
				branch = Registry.CurrentUser;
			}
			else if (LeftCompare(key, "HKEY_LOCAL_MACHINE\\", out remainder))
			{
				branch = Registry.LocalMachine;
			}
			else if (LeftCompare(key, "HKEY_USERS\\", out remainder))
			{
				branch = Registry.Users;
			}
			else if (LeftCompare(key, "HKEY_CURRENT_CONFIG\\", out remainder))
			{
				branch = Registry.CurrentConfig;
			}
#endif
			if (branch == null)
			{
				throw new Exception("Invalid Registry Root specified in: " + key);
			}
			return branch.CreateSubKey(remainder);
		}

		private void	RegisterKeys(ArrayList keys)
		{
			RegistryKey key = Registry.ClassesRoot;
			string lastkey = "";
			string attr = "";
			string value = "";
			foreach(RegistryValue r in keys)
			{
				if (r.key != lastkey)
				{
					if (lastkey != "") key.Close();
					lastkey = r.key;
					key = CreateSubKey(lastkey);
				}

				attr = (r.attr == null) ? "" : r.attr;
				value = (r.value == null) ? "" : r.value.ToString();

				// The Reg API on Some OS's requires the length to include the terminating \0 and on others not too.
				// The /0 doesn't seem to do much harm so I am adding it anyway
				value += "\0";
				key.SetValue(attr, value);
			}

			key.Close();
			return;
		}

		public	static	bool IsWin2K()
		{
			OperatingSystem os = Environment.OSVersion;
			if ( os.Platform == PlatformID.Win32NT && os.Version.Major == 5)
				return true;
			else
				return false;
		}

		private	static	bool IsWindows()
		{
			OperatingSystem os = Environment.OSVersion;
			if ( os.Platform == PlatformID.Win32Windows)
				return true;
			else
				return false;
		}


		private object[]	GenerateRegKeysForType(Type t, bool proxystubmarshaler, bool codebase, string remotehost, string runas)
		{
			ArrayList	keys = new ArrayList();

			// If the type is not public then no registration.
			if ((t.IsPublic || t.IsNestedPublic) && !t.IsImport)
			{
				string root = Registry.ClassesRoot.Name;
				string key = null;
				string strTypeName = t.FullName;
				string strAssemblyName = t.Assembly.GetName().FullName;

				// Deal with classes
				if (t.IsClass)
				{
					if(!(t.IsAbstract || t.GetConstructor(BindingFlags.Instance | BindingFlags.Public, null, new Type[0], null) == null))
					{
						// Creatable class - get Guid and ProgId
			            string strClsId = "{" + Marshal.GenerateGuidForType(t).ToString().ToUpper(CultureInfo.InvariantCulture) + "}";
						object[] attrs = t.GetCustomAttributes(typeof(ProgIdAttribute), true);

						// ====================================================================================
						// Generate progid
						// If there is no prog ID attribute then use the full name of the type as the prog id.
						// null progid = is equivalent to a blank progid.
						// ====================================================================================
			            string strProgId = null;
						strProgId = (attrs.Length == 0) ?  strTypeName : ((ProgIdAttribute)attrs[0]).Value;

						if (strProgId == null)
							strProgId = String.Empty;

						// Now generate registry keys
						// Create the HKEY_CLASS_ROOT\<strProgId> key.
						if (strProgId != String.Empty)
						{
							key = root + "\\" + strProgId;
							keys.Add(new RegistryValue( key, "", strTypeName));
							keys.Add(new RegistryValue( key + "\\CLSID", "", strClsId));
						}

				        // Create the HKEY_CLASS_ROOT\CLSID\<CLSID> key.
						key = root + "\\CLSID\\" + strClsId;
						keys.Add(new RegistryValue( key, "", strTypeName));
						keys.Add(new RegistryValue( key, "AppID", strClsId));

				        // Create the HKEY_CLASS_ROOT\CLSID\<CLSID>\InprocServer32 key.
						key = root + "\\CLSID\\" + strClsId + "\\InprocServer32";
						keys.Add(new RegistryValue( key, "", strMsCorEEFileName));
						keys.Add(new RegistryValue( key, "ThreadingModel", "Both"));
						keys.Add(new RegistryValue( key, "Class", strTypeName));
						keys.Add(new RegistryValue( key, "Assembly", strAssemblyName));
						keys.Add(new RegistryValue( key, "RuntimeVersion", strRuntimeVersion));
						if(codebase)
							keys.Add(new RegistryValue( key, "Codebase", t.Assembly.CodeBase));

						if (strProgId != String.Empty)
						{
							key = root + "\\CLSID\\" + strClsId + "\\ProgId";
							keys.Add(new RegistryValue( key, "", strProgId));
						}


			            // Create the HKEY_CLASS_ROOT\AppID\.
						key = root + "\\AppID\\" + strClsId;
						keys.Add(new RegistryValue( key, "", strTypeName));
						keys.Add(new RegistryValue( key, "AppID", strClsId));

						if(remotehost == "")
						{
							keys.Add(new RegistryValue( key, "DllSurrogate", ""));
						}
						else
						{
							keys.Add(new RegistryValue( key, "RemoteServer", remotehost));
						}

						if(runas == "")
						{
							keys.Add(new RegistryValue( key, "RunAs", "Interactive User"));
						}
						else
						{
							keys.Add(new RegistryValue( key, "RunAs", runas));
						}

			            // Create the HKEY_CLASS_ROOT\CLSID\<CLSID>\Implemented Categories\<Managed Category Guid> key.
						string cat = root + "\\CLSID\\" + strClsId + "\\Implemented Categories\\" + strManagedCategoryGuid;
						keys.Add(new RegistryValue( cat, "", ""));

						// Create the HKEY_CLASS_ROOT\Component Category key.
						string comp = root + "\\Component Category\\" + strManagedCategoryGuid;
						keys.Add(new RegistryValue( comp, "0", ""));

						if (proxystubmarshaler)
						{
							string name;
							Guid   guid;
							if (GetIClassXXXInfo(t, out name, out guid))
							{
								string itf = root + "\\Interface\\{" + guid.ToString().ToUpper(CultureInfo.InvariantCulture) + "}";
								keys.Add(new RegistryValue( itf, "", name));
								keys.Add(new RegistryValue( itf, "Assembly", strAssemblyName));
								keys.Add(new RegistryValue( itf, "TypeName", strTypeName));
								keys.Add(new RegistryValue( itf + "\\ProxyStubClsId", "", strPrxyStubMarshalerGuid));
								keys.Add(new RegistryValue( itf + "\\ProxyStubClsId32", "", strPrxyStubMarshalerGuid));
								if(codebase)
									keys.Add(new RegistryValue( itf, "Codebase", t.Assembly.CodeBase));

								// We may want to add some unregistration guff here.
							}
						}
					}
				}
				else if(t.IsInterface)
				{
					if (proxystubmarshaler)
					{
						string itf = root + "\\Interface\\{" + Marshal.GenerateGuidForType(t).ToString().ToUpper(CultureInfo.InvariantCulture) + "}";
						keys.Add(new RegistryValue( itf, "", t.Name));
						keys.Add(new RegistryValue( itf, "Assembly", strAssemblyName));
						keys.Add(new RegistryValue( itf, "TypeName", strTypeName));
						keys.Add(new RegistryValue( itf + "\\ProxyStubClsId", "", strPrxyStubMarshalerGuid));
						keys.Add(new RegistryValue( itf + "\\ProxyStubClsId32", "", strPrxyStubMarshalerGuid));
						if(codebase)
							keys.Add(new RegistryValue( itf, "Codebase", t.Assembly.CodeBase));
					}
				}
			}
			return (object[])keys.ToArray();
		}

		public void RegisterAssembly(Assembly asm, string regfile, bool proxystubmarshaler, bool codebase, string remotehost, string runas)
		{

			ArrayList keys = new ArrayList();
			foreach(Type t in GetTypes(asm))
			{
				if (Marshal.IsTypeVisibleFromCom(t))
				{
					foreach(object o in RegisterType(t, proxystubmarshaler, codebase, remotehost, runas))
					{
						keys.Add(o);
					}
				}
			}
			if(regfile != null)
				GenerateRegFile(keys, regfile);
			else
				RegisterKeys(keys);
		}

		public void RegisterAssembly(string assemblyfile, string regfile, bool proxystubmarshaler, bool codebase, string remotehost, string runas)
		{
			RegisterAssembly(Assembly.LoadFrom(assemblyfile), regfile, proxystubmarshaler, codebase, remotehost, runas);
		}
	}


	public class ComRegister : ProgramBase
	{
		const	string	exe = ".exe";
		const	string	dll = ".dll";

		static ComRegister()
		{
			name	= "ComReg";
			description = "Assembly Registration utility";
			syntax	= name + "[Options] Assembly file [Options]";
			where	= "Assembly file\t\tFile containing the assembly to register";
			options = new Option[]	{
					  new Option ("/codebase","/c",				"\tSpecify that this assembly may be shared without the use of the gac.", false),
					  new Option ("/gac","/g",				"\t\tAdd (Remove when used in with /unregister) the assembly to the GAC.", false),
					  new Option ("/proxystubmarshaler","/p","Marshal using the proxystub marshaler.", false),
					  new Option ("/regfile","/regf",		"\tEmit a regfile.", true),
					  new Option ("/remotehost:server","/rem:server","\n\t\t\t\tSpecify the remote server which will host types in this Assembly.", true),
					  new Option ("/runas:user","/run:server","Specify the user name to run this app when used out of process.", true),
					  new Option ("/unregister", "/u",		"\tUnregister the assembly.", false)
					};
		}

		static public	void RegisterAssembly(string assemblyfile, string regfile, bool proxystubmarshaler, bool codebase, string remotehost, string runas)
		{
			string adname = "ComReg: " + Guid.NewGuid().ToString();
			string filepath = Path.GetDirectoryName(Path.GetFullPath(assemblyfile));
			string filename = Path.GetFullPath(assemblyfile);

			Evidence si = null;
            AppDomain ad = AppDomain.CreateDomain(adname, si);
            if (ad == null)
                throw new ApplicationException("Unable to create AppDomain for assembly cache install ");
			ComRegistrationClass r = (ComRegistrationClass)ad.CreateInstanceAndUnwrap(Assembly.GetAssembly(typeof(ComReg.ComRegistrationClass)).FullName, typeof(ComReg.ComRegistrationClass).FullName);
			r.RegisterAssembly(filename, regfile, proxystubmarshaler, codebase, remotehost, runas);
			AppDomain.Unload(ad);
		}


		public static int ComRegisterMain(string[] args)
		{
			int		exitcode = 0;
			String	assemblyfile = null;
			bool	psmarshaler = false;
			string	regfile = null;
			bool	gac = false;
			bool	unregister = false;
			bool	codebase = false;
			string	remotehost = "";
			string	runas = "";

			try
			{
				Setting[] switches = GetSwitches(args);

				foreach(Setting sw in switches)
				{
					if (sw.option == null)
					{
						if (assemblyfile != null)
						{
							// We already have an assembly
							PrintLogo();
							WriteErrorMsg("Only one assembly can be registered at a time.");
							exitcode = 1;
							goto done;
						}
						else
						{
							// We are cool with this
							assemblyfile = sw.value;
						}
					}
					else
					{
						if (CompareString(sw.option.value, "/proxystubmarshaler")==0)
						{
							psmarshaler=true;
						}
						else if (CompareString(sw.option.value, "/regfile:")==0)
						{
							if (regfile != null)
							{
								PrintLogo();
								WriteErrorMsg("Only one regfile can be specified at a time.");
								exitcode = 1;
								goto done;
							}
							else
								regfile = sw.value;
						}
						else if (CompareString(sw.option.value, "/gac")==0)
						{
							gac = true;
						}
						else if (CompareString(sw.option.value, "/unregister")==0)
						{
							unregister = true;
						}
						else if (CompareString(sw.option.value, "/codebase")==0)
						{
							if(unregister)
							{
								//Codebase and unregister are mutually exclusive
								PrintLogo();
								WriteErrorMsg("Codebase can not be specified with unregister.");
								exitcode = 1;
								goto done;
							}
							if(gac)
							{
								//Codebase and gac are mutually exclusive
								PrintLogo();
								WriteErrorMsg("Codebase can not be specified with gac.");
								exitcode = 1;
								goto done;
							}
							codebase = true;
						}
						else if (CompareString(sw.option.value, "/remotehost:")==0)
						{
							remotehost = sw.value;
						}
						else if (CompareString(sw.option.value, "/runas:")==0)
						{
							runas = sw.value;
						}
						else if (CompareString(sw.option.value, "/?")==0)
						{
							PrintLogo();
							PrintUsage();
							exitcode = 0;
							goto done;
						}
					}
				}

				PrintLogo();
				if (assemblyfile == null)
				{
					WriteErrorMsg("No Assembly file specified");
					exitcode = 1;
					goto done;
				}
				else
				{
					// Deal with adding to or removing from the gac ... as required
					if (gac)
					{
						if(unregister)
						{
//							WriteInfoMsg("Warning uninstall currently only removes the file from the GAC");

							string fullname = FusionInstall.FullAssemblyName(assemblyfile);
							if (FusionInstall.RemoveAssemblyFromCache(fullname) != 0)
							{
								WriteErrorMsg("Failed to remove the assembly: " + assemblyfile + "[" + fullname + "] from the GAC");
								exitcode = 1;
							}
						}
						else
						{
							if(!File.Exists(assemblyfile))
							{
								WriteErrorMsg("Unable to locate assembly: " + assemblyfile);
								exitcode = 1;
							}
							else if(FusionInstall.AddAssemblyToCache(assemblyfile) != 0)
							{
								WriteErrorMsg("Failed to add the assembly: " + assemblyfile + " to the GAC");
								exitcode = 1;
							}
						}
					}

					// If no errors installing
					if (exitcode == 0)
					{

						if(unregister)
						{
						//	WriteInfoMsg("Assembly: " + assemblyfile + " unregistered" + ((gac) ? " and removed from the GAC." : "."));
						}
						else
						{
							RegisterAssembly(assemblyfile, regfile, psmarshaler, codebase, remotehost, runas);
						//	WriteInfoMsg("Assembly: " + assemblyfile + " registered" + ((gac) ? " and added to the GAC." : "."));
						}
					}
				}
			}
			catch(Exception e)
			{
				WriteErrorMsg(e.Message);
				return 1;
			}
		done:
			return exitcode;
		}
	}
}
