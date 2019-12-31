<?php include 'dbconfig.php'; ?>
<?php include 'links.php' ; ?>
<?php include 'config.php'; ?>
<?php session_start(); ?>

<?php if(isset($_SESSION['user'])){ ?>

<?php if(isset($_GET['new'])){
			$query = "select count(*) from CLASS where UID=".$_SESSION['user'];
			$stmt = $conn->prepare($query);
			$stmt->bind_result($num_classes);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();

			if($num_classes > $CREATE_CLASS_LIMIT){
				$warning = "Maximum number of classes reached";
			}else{
				$uid = $_SESSION['user'];
				$cname = $_GET['cname'];
				$query = "insert into CLASS (UID,CNAME) values (?,?)";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('is',$uid,$cname);
				$stmt->execute();
				$stmt->close();
		  }
		}
		if($_GET['mode']=='save'){
			$cname = $_GET['cname'];
			$cid = $_GET['cid'];
			$query = 'update CLASS set CNAME=? where CID=? and  UID='.$_SESSION['user'];;
			$stmt = $conn->prepare($query);
			$stmt->bind_param('si',$cname,$cid);
			$stmt->execute();
			$stmt->close();
		}
		if($_GET['mode']=='delete'){
			$cid = $_GET['cid'];
			$query = 'delete from CLASS where cid=? and  UID='.$_SESSION['user'];;
			$query2 = 'delete from ASSIGNMENT where cid=? and cid in (select CID from CLASS where UID='.$_SESSION['user'].')';
			$stmt = $conn->prepare($query);
			$stmt2 = $conn->prepare($query2);
			$stmt->bind_param('i',$cid);
			$stmt2->bind_param('i',$cid);
			$stmt->execute();
			$stmt2->execute();
			$stmt->close();
			$stmt2->close();
		}
?>

<html>
	<head>
		<title>View Classes</title>

		<link rel='stylesheet' style='text/css' href='css/main.css'>
	</head>
	<body>
		<a href='login.php'>Logout</a>&nbsp;<a href='index.php'>Home</a>
		<h1>View Classes</h1>
		<span style='color:red;'><?php echo $warning; ?></span>
		<table>
			<tr class='table_header'><th class='table_header'>Class</th><th class='table_header'>Edit</th></tr>
			<?php
				$query = 'select * from CLASS  where UID='.$_SESSION['user'];;
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$result = $stmt->get_result();

				while($row = $result->fetch_assoc()){ ?>

					<?php if($_GET['mode']<>'update' or $row['CID']<>$_GET['cid']){ ?>

					<tr><td><a href='<?php echo lc_select($row['CID'])?>'>
						<?php echo $row['CNAME']; ?></a></td>
						<td><input type='button' value='edit' onclick="javascript: window.location.href = 'view_class.php?cid=<?php echo $row['CID'] ?>&mode=update';" /></td></tr>

					<?php }else { ?>
					<form action='view_class.php' method='get'>
						<tr>
							<input type='hidden' name='cid' value='<?php echo $row['CID']; ?>'>
							<td><input type='text' name='cname' value='<?php echo $row['CNAME']; ?>'></td>
							<td><input type='submit' name='mode' value='save'></td>
							<td><input type='submit' name='mode' value='delete'></td>
						</tr>
					</form>
					<?php } ?>
			<?php	} ?>

			<form action='view_class.php' method='get'>
				<tr>
					<td><input type='text' name='cname' /></td>
					<td><input type='submit' name='new' /></td>
				</tr>
			</form>
		</table>
	</body>
	<footer>

	</footer>
</html>

<?php }else{ header("location: login.php"); } ?>
