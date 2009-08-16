<?
include "../../sas.inc.php";
sas_start("Gruppe loeschen","../../../","/admin/admin",1);
sas_showmenu();

error_reporting(15);
qbe_restrict_access("groupadm");

sas_varimport('group');
sas_varimport('class');
sas_varimport('confirm');

$class = strtoupper($class);
$group = strtoupper($group);

if ( ($group == "") || ($class == "") )
{
	sas_pcode('error','Ung&uuml;ltige Parameter');
	sas_end();
}

?>
Klasse: <em><?=$class?></em><br/>
Gruppe: <em><?=$group?></em><br/>
<br/>
<?php

if ($confirm == 1)
{

	//
	// global $class, $sas_ldap_server, $sas_ldap_adminuser, $sas_ldap_adminpass, $PHP_SELF, $showuid;

        $ldap = ldap_connect($sas_ldap_server);
        ldap_bind( $ldap , $sas_ldap_adminuser , $sas_ldap_adminpass );

        if ( @ldap_delete( $ldap , "cn=$class-$group,ou=$class,ou=Classes,ou=People,".$sas_ldap_base) )
	{
		sas_pcode('success',"$class-$group wurde gel&ouml;scht.");
	} else {
		sas_pcode('error',ldap_error($ldap));
	}
	
} else {

	?>
	<form method=post>
	<input type=hidden name="class" value="<?=$class?>" />
	<input type=hidden name="group" value="<?=$group?>" />
	<input type=checkbox name="confirm" value="1"> Ja, L&ouml;schen<br/>
	<br/>
	
	<button type=submit>Okay.</button>
	</form>
	<?php

}

sas_end();
