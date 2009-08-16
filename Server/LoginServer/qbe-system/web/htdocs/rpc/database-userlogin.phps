<?php
	//
	// Qbe SAS RPC Documentation
	//
	// /rpc/database-userlogin
	//

	// the following parameters are recognized:
	//
	//   ip      REQUIRED
	//   group   OPTIONAL
	//
	//   ip      The Client IP to be checked. If empty, unknown will be returned.
	//   group   If set and a valid user is logged in, it will be checked against this group.
	//           If the user is not a group member, unauthorized will be returned.

	// return values
	//
	//   username              The user logged in from this client ip.
	//   *UNKNOWN*             The user is not known.
	//   *UNAUTHORIZED*        The user is logged in but not a group member.


	// As this is a normal http based Qbe SAS RPC API, you can use the following PHP call:
	function qbe_rpc_database_userlogin($ip,$group)
	{
		$status = -1;
		$result = file('http://qbe-auth.htlwrn.ac.at/rpc/database-userlogin?ip='.$ip.'&group='.$group);
		if ($result[0] == '*') { $status = -2; } else { $status = $result; }
		if ($result == '*UNKNOWN*') { $status = -3; }
		if ($result == '*UNAUTHORIZED*') { $status = -4; }
		return $status;
	}
?>
