<?
include "../../sas.inc.php";

sas_start('System Changelog',"../../","/modules/changelog",1);
sas_showmenu();
qbe_restrict_access('sysops');

	$db = mysql_connect($sas_mysql_server,$sas_mysql_user,$sas_mysql_password);

	if (sas_ldap_isadmin($user))
	{
		$newdesc = isset($_POST['desc']) ? $_POST['desc'] : '';
		if ($newdesc != '')
		{
			$sql = 'INSERT INTO changelog.changelog(who,date,descr) VALUES ("' . sas_ldap_getuid($user) . '",NOW(),"' . $newdesc . '");';
			$res = mysql_query($sql);
			echo mysql_error();

			?><meta http-equiv="refresh" content="0; url=<?=$_SERVER['PHP_SELF']?>"><?php
		}
	}

	$query = mysql_query("SELECT * FROM changelog.changelog ORDER BY date DESC");
	echo mysql_error();
	
	$old_date = '';
	$old_who = '';
	
	if (sas_ldap_isadmin($user))
	{	$old_who = $userid;
		$old_date = strftime("%Y-%m-%d",time());
	?>
	<b><?=$old_date?></b><br>
	<?=$old_who?>:<br>
	
		<form method=post>
		<input name=desc size=80 style="margin-left: 50px;">
		<button type=submit style="margin-left: 50px;">Speichern</button><br/>
		</form>
	<?php
	}

	while ($row = mysql_fetch_array($query))
	{	
		if($row['date'] != $old_date)
		{ 
			$old_who = '';
			?>
				<br/><b><?=$row['date']?></b><br/>
			<?php
		}
		if($row['who'] != $old_who)
		{ 	?>
			<span style="z-index: 99;"><?=$row['who']?>:</span>
			<?php
		}
		?>
	<code style="margin-left: 50px;">
		<?=$row['descr']?>
		<br/>
	</code>
		<?php
		
		$old_date = $row['date'];
		$old_who = $row['who'];
	}


	?>
	<br/>
	<a href="prettyprint">Pretty Print Ausgabe</a><br/>
	
	<?php

	mysql_close($db);

sas_end();
?>
