<?
	include("../../sas.inc.php");
	sas_start("Dateizugriff","../../","/modules/filexs",1,0);

	error_reporting(15);
	include("inc.php");
	
	// parameter checks
	$thisfile = $path.$_FILES['uploadfile']['name'];

	echo 'Ziel: '.$thisfile.'<br><br>';

	if (!is_file($thisfile))
	{
		if (is_uploaded_file($_FILES['uploadfile']['tmp_name']))
		{
			$xf = escapeshellarg($_FILES['uploadfile']['tmp_name']);
			system("cp ".$xf." /tmp/foobar");
			$cmd = "/qbe/sbin/qbe-filexs ".$userid." - fileput ".escapeshellarg($thisfile)." < ".$xf;
			// echo 'Call: '.$cmd.'<br>';
			system($cmd);
			if (!is_file($thisfile))
			{
			?>
			<span class="error">Die Datei konnte nicht erstellt werden.</span>
			<?
			} else {
			?>
			Datei wurde abgespeichert.<br>
			<?
			}
	
		} else {
			/*print "Possible file upload attack!  Here's some debugging info:\n"; */
		?>
			<span class="error">Fehler beim Upload der Datei aufgetreten.</span>
			<br />
			<br />
		<?
			makeBackLink();
		}
	} else {
	?>
		<span clasS="error">Die Datei existiert bereits und wird nicht &uuml;berschrieben.</span>
		<br />
		<br />
	<?
		makeBackLink();
	}
	sas_end();
