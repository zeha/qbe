<html>
<head>
	<title>Qbe SAS Hilfe</title>
	<style>
		p,td,body,th { color: white; font-family: Tahoma,Verdana,sans-serif; vertical-align: top; font-size: 8pt;}
		a { color: white; text-decoration: underline; }
	</style>
</head>
<body style="background-color: #336699;">

<?

error_reporting(0);

$topic = ( isset($_GET['topic']) ? $_GET['topic'] : 'index' );

$topic = str_replace('/','',$topic);
$topic = str_replace('.','',$topic);
$topic = str_replace('\\','',$topic);
$topic = str_replace('%','',$topic);

$topic = './topics/'.$topic.'.html';

foreach(file($topic) as $line)
{ echo $line; }

?>

</body>
</html>

