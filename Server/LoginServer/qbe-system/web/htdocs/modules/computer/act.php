<?php
	// version 1.0
	//
	// todo: fix policy object name passing

	require "../../sas.inc.php";
	sas_start("Computer bearbeiten","../../","/admin",1);
	
	sas_varimport("action");
	sas_varimport("pcs");
	sas_varimport("policy");
	sas_varimport("owner");
	sas_varimport("dontask");

	if (!is_array($pcs))
	{
		sas_pcode('error','Kein Eintrag ausgew&auml;hlt');
		sas_end();
	}

	$ok = false;
	if ($action == 'delete') 
	{	$ok = true; $text = "L&ouml;schen"; }
	if ($action == 'change')
	{	$ok = true; $text = "&Auml;ndern"; }
	
	if (!$ok)
	{
		sas_pcode('error','Ungueltige Parameter');
		sas_end();
	}

	$error = false;
	$objects = array();

	$ds = ldap_connect($sas_ldap_server);
	$b = false;
	if ($ds) $b = ldap_bind($ds,$sas_ldap_adminuser,$sas_ldap_adminpass);
	if ($b)
	{

		foreach($pcs as $entry)
		{
			$this_err = "";
			
			$sr = ldap_search($ds,$sas_ldap_base,"(uid=".$entry.")");
			$res = ldap_get_entries($ds,$sr);
			if ($res["count"] > 0)
			{
				$this_owner = $res[0]["owner"][0];
				if ($this_owner != $user)
				{
					$this_err = "Not your own.";
				}
				if (!isset($res[0]["qbepolicyname"]))
					$res[0]["qbepolicyname"][0] = '';
			
				$new_policy = find_policy($policy[$entry]);
				if ( ($this_err == "") && ($new_policy == "") && ($policy[$entry] != "") ) { $this_err = "Policy object invalid"; }
				$new_owner = find_user($owner[$entry]);
				if ( ($this_err == "") && ($new_owner == "") ) { $this_err = "Owner object invalid"; }
			
				array_push($objects,array(	"name" => $entry,
								"found" => true,
								"error" => $this_err,
								"owner" => $this_owner,
								"dn" => $res[0]["dn"],
								"policy" => $res[0]["qbepolicyname"][0],
								"new-policy" => $new_policy,
								"new-owner" => $new_owner
								));
				
		
			} else { 
				$error = true;
				$errstr = "Kann PC-Objekt nicht finden!"; 
			}

		}

		if (!$error)
		{
			?>
			<h2><?=$text?></h2>
			<b>Ausgew&auml;hlte Objekte:</b>
			<form method=post>
			<?php
			qbe_web_maketable(true);
			?>
			<tr>
			<th>Name</th>
			<th>(?)</th>
			<th colspan=2>Eigent&uuml;mer &amp; neu</th>
			<th colspan=2>Policy &amp; neu</th>
			</tr>
			<?php
			
			foreach($objects as $object)
			{
				if ($object['error'] == '')
				{
				if ($dontask == "doit")
				{
					if ($action == 'delete')
					{
						$object['error'] = object_delete($object);
					}
					if ($action == 'change')
					{
						$object['error'] = object_save($object);
					}
				} else {
					?>
					<input type=hidden name="pcs[<?=$object['name']?>]" value="<?=$object['name']?>" />
					<input type=hidden name="policy[<?=$object['name']?>]" value="<?=$policy[$object['name']]?>" />
					<input type=hidden name="owner[<?=$object['name']?>]" value="<?=$object['new-owner']?>" />
					<?php
				}
				}
			
				qbe_web_maketr();

				?>
				<td><?=$object['name']?></td>
				<td bgcolor="<? if($object['error']!='') { echo 'red'; }?>"><?=$object['error']?></td>
				<td><?=sas_ldap_getusername($object['owner'])?></td>
				<td><?=sas_ldap_getusername($object['new-owner'])?></td>
				<td><?=$object['policy']?></td>
				<td><?=$object['new-policy']?></td>
				</tr>
	<?php
			}
			if ($dontask != "doit")
			{
				print "<tr><td colspan=6 align=right><button type=submit value=doit name=dontask>OKAY</button><input type=hidden name=action value=\"".$action."\"></td></tr>";
			}
			print "</table></form>";
		}
		
	} else {
		$error = true;
		$errstr = "Logon Error";
	}

	ldap_close($ds);

	if ($error)
	{
		sas_pcode('error',($errstr == '') ? 'Ein Fehler ist aufgetreten.' : $errstr);
	}

	sas_end();

	function find_policy($policy)
	{
		$dn = qbe_ldap_getobjectdn("(& (cn=".$policy.") (objectClass=qbeHostPolicy))");
		return $dn;
	}

	function find_user($user)
	{
		$udn = sas_ldap_getdn($user); 
		if ($udn == "")
		{
			if (sas_ldap_getuid($user) != "")
				return $user;
			else
				return "";
		} else return $udn;
	}

	function object_delete($object)
	{
		global $ds;

		$error = !ldap_delete($ds,$object["dn"]);
		$ret = "PC konnte nicht gel&ouml;scht werden.";
		
		if (!$error)
		{
			$ret = "";
			qbe_log_text("qbe-appmodule-hosts-userworkstation",LOG_NOTICE,"Deleted workstation: ".$object["dn"]." for ".$_SESSION['user']);
		}
		
		return $ret;
	}

	function object_save($object)
	{
		global $ds;

		$info = array();

		if ($object['owner'] != $object['new-owner'])
		{
			$info['owner'] = $object['new-owner'];
		}
		if ($object['policy'] != $object['new-policy'])
		{
			$info['qbePolicyName'] = $object['new-policy'];
		}
		if ($info == array())
		{
			$ret = "Keine &Auml;nderungen";
			$error = true;
		} else {
		
			$error = !ldap_modify($ds,$object["dn"],$info);
			$ret = "PC konnte nicht ge&auml;ndert werden.";
		}

		if (!$error)
		{
			$ret = "";
			qbe_log_text("qbe-appmodule-hosts-userworkstation",LOG_NOTICE,"Modified workstation: ".$object["dn"]." for ".$_SESSION['user']);
		}
		
		return $ret;
	}

