<?php include 'dbconfig.php' ?>
<?php include 'config.php' ?>

<?php
		$username = $_POST['username'];
		$password = $_POST['password'];
		$password2 =$_POST['password2'];

		$warning = '';
		if($_POST['mode']<>'create'){
			if($password<>$password2) $warning = 'The passwords you typed do not match';
		}else {
			$query = 'select count(*) from USERINFO';
			$stmt = $conn->prepare($query);
			$stmt->bind_result($num_accounts);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			if($num_acounts > $CREATE_ACCOUNT_LIMIT ){
				$warning = "Maximum number of accounts reached alrady.";
			}else{
				$query = 'select username from USERINFO where username=?';
				$stmt = $conn->prepare($query);
				$stmt->bind_param('s',$username);
				$stmt->execute();
				if($stmt->affected_rows > 0){
					$warning = 'The username $username already exists';
				} else {
					$stmt->close();
					$query = 'insert into USERINFO (username,password) values (?,?)';
					$stmt = $conn->prepare($query);
					$stmt->bind_param('ss',$username,$password);
					$stmt->execute();
					$stmt->close();

					header('location: login.php');
				}
			}
		}


?>

<html>
	<head>
		<title>Create An Account</title>
	</head>
	<body>
		<h1>Create An Account</h1>
		<span style='color:red;'><?php echo $warning; ?></span>
		<form action='create_account.php' method='post'>
		<table>
			<tr><th>Username</th><td><input type='text' name='username'></td></tr>
			<tr><th>Password</th><td><input type='password' name='password'></td></tr>
			<tr><th>Re-Enter Password</th><td><input type='password' name='password2'></td></tr>
			<tr><td><input type='submit' name='mode' value='create' /></td></tr>
		</table>
		</form>
	</body>
	<footer>

	</footer>
</html>
