<?php include 'dbconfig.php'; ?>
<?php session_start(); session_destroy(); ?>

<?php
	$username = $_POST['username'];
	$password = $_POST['password'];
	$login = $_POST['login'];
	
	if(isset($login)){
		$query = "select uid, username, password from USERINFO where username=?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param("s",$username);
		$stmt->bind_result($r_uid,$r_username,$r_password);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		if($password == $r_password){
			session_start();
			$_SESSION['user'] = $r_uid;
			header("location: index.php");
		}
	}
?>

<html>
	<header>
		<Title>Login Page</Title>
	</header>
	<body>
		<h1> Homework Manager Login</h1>
		<table>
			<form method='post' action='login.php'>
				<tr><th>Username:</th><td><input type='text' name='username' /></td></tr>
				<tr><th>Password:</th><td><input type='password' name='password' /></td></tr>
				<tr><td><input type='submit' name='login'></td></tr>
			</form>
		</table>
		
		<a href='create_account.php'>Create An Account</a>
	</body>
	<footer>
		
	</footer>
</html>