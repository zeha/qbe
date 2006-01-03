using System;
using NetFwTypeLib;

namespace SetupWindowsFirewall
{
	public class SetupFirewall
	{
		public SetupFirewall()
		{
		}

		public bool InstallSettings()
		{
			try 
			{
				// go get ICS Manager Object
				INetFwMgr fwmgr = null;
				System.Object FwMgrObject;
				System.Type FwMgrType;
				FwMgrType = System.Type.GetTypeFromCLSID(new System.Guid("{304CE942-6E39-40D8-943A-B913C40C9CD4}"));
				FwMgrObject = Activator.CreateInstance(FwMgrType);
				fwmgr = (NetFwTypeLib.INetFwMgr)FwMgrObject;

				// create new port object
				INetFwOpenPort fwport = null;
				System.Object FwPortObject;
				System.Type FwPortType;

				FwPortType = System.Type.GetTypeFromCLSID(new System.Guid("{0CA545C6-37AD-4A6C-BF92-9F7610067EF5}"));
				FwPortObject = Activator.CreateInstance(FwPortType);
				fwport = (INetFwOpenPort)FwPortObject;
				fwport.Enabled = true;
				fwport.IpVersion = NET_FW_IP_VERSION_.NET_FW_IP_VERSION_ANY;
				fwport.Name = "Qbe Application Transport";
				fwport.Port = 7666;
				fwport.Protocol = NET_FW_IP_PROTOCOL_.NET_FW_IP_PROTOCOL_TCP;
				fwport.Scope = NET_FW_SCOPE_.NET_FW_SCOPE_ALL;

				// add it
				fwmgr.LocalPolicy.CurrentProfile.GloballyOpenPorts.Add(fwport);

				// set icmp options
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowInboundEchoRequest = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowInboundMaskRequest = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowInboundRouterRequest = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowInboundTimestampRequest = true;

				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowOutboundDestinationUnreachable = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowOutboundTimeExceeded = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowOutboundPacketTooBig = true;
				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowOutboundParameterProblem = true;

				fwmgr.LocalPolicy.CurrentProfile.IcmpSettings.AllowRedirect = true;

				return true;
			} 
			catch (Exception ex)
			{
				ex=ex;
				return false;
			}
		}
	}
}
