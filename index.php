<?php include 'dbconfig.php'; ?>
<?php session_start(); ?>

<?php if(isset($_SESSION['user'])){ ?>
<?php echo $_SESSION['cid'] = ''; ?>
<?php include 'links.php'; ?>
<html>
	<header>
		<title>Homework Manager</title>

		<link rel='stylesheet' style='text/css' href='css/main.css'>
	</header>
	<body>
		<a href='login.php'>Logout</a>
		<h1>Homework Manager</h1>
		<table>
			<tr class='table_header'><th class='table_header'>Menu</th></tr>
			<tr><td><a href='<?php echo $la_upcoming; ?>'>Assignments</a></td></tr>
			<tr><td><a href='<?php echo $lc; ?>'>Classes</a></td></tr>
		</table>
	</body>
	<footer>
	</footer>
</html>


<?php }else{ header("location: login.php"); } ?>