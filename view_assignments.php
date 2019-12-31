<?php include 'dbconfig.php'; ?>
<?php include 'links.php' ; ?>
<?php session_start(); ?>

<?php if(isset($_SESSION['user'])){ ?>


<?php $success_note = ""; ?>

<?php
	function select_class($conn,$class){
		$query = "select CID, CNAME from CLASS where UID=".$_SESSION['user'];
		$stmt = $conn->prepare($query);
		$stmt->bind_result($cid,$cname);
		$stmt->execute();
		echo "<select name='class'>";
		while($stmt->fetch()){ ?>
			<option value='<?php echo $cid; ?>' <?php if($cname==$class) echo 'selected'; ?>><?php echo $cname; ?></option>
		<?php }
		echo "</ select>";
	}
	function text_assignment($aname){ ?>
		<input type='text' name='aname' value='<?php echo $aname; ?>'/>
	<?php }
	function text_descr($adescr){ ?>
		<textarea name='adescr' >
			<?php echo $adescr; ?>
		</textarea>
	<?php }
	function date_dassigned($dassign){ ?>
		<input type='date' name='date_assigned' value='<?php echo $dassign; ?>'/>
	<?php }
	function date_ddue($ddue){ ?>
		<input type='date' name='date_due' value='<?php echo $ddue; ?>'/>
	<?php }
	function number_gi($gi){ ?>
		<input type='number' name='grade_impact' value='<?php echo $gi; ?>'/>
	<?php }
	function number_hw($hw){ ?>
		<input type='number' name='hours_worked' value='<?php echo $hw; ?>'/>
	<?php }
	function select_status($status){ ?>
		<select name='status'>
			<option value='NS' <?php if($status == 'NS'){ echo "selected"; } ?>>Not Started</option>
			<option value='IP' <?php if($status == 'IP'){ echo "selected"; } ?>>In Progress</option>
			<option value='CP' <?php if($status == 'CP'){ echo "selected"; } ?>>Complete</option>
		</select>
	<?php	} ?>

<?php

	if($_GET['mode']=='hide'){
		$query = "update ASSIGNMENT set hide=1 where aid=? and cid in (select CID from CLASS where UID=".$_SESSION['user'].")";
		$aid = $_GET['aid'];
		$stmt = $conn->prepare($query);
		$stmt->bind_param("i",$aid);
		$stmt->execute();
	}
	if($_GET['mode'] == 'unhide'){
		$query = "update ASSIGNMENT set hide=0 where aid=? and cid in (select CID from CLASS where UID=".$_SESSION['user'].")";
		$aid = $_GET['aid'];
		$stmt = $conn->prepare($query);
		$stmt->bind_param("i",$aid);
		$stmt->execute();
	}
	if($_GET['mode'] == 'save'){
		$query = "update ASSIGNMENT set CID=?, ANAME=?, ADESCR=?, DATE_ASSIGNED=?, DATE_DUE=?, ASTATUS=?, GRADE_IMPACT=?, HOURS_WORKED=? WHERE AID=? and cid in (select CID from CLASS where UID=".$_SESSION['user'].")";

		$cid = $_GET['class'];
		$aname = $_GET['aname'];
		$adescr= $_GET['adescr'];
		$date_assigned = $_GET['date_assigned'];
		$date_due = $_GET['date_due'];
		$astatus = $_GET['status'];
		$grade_impact = $_GET['grade_impact'];
		$hours_worked = $_GET['hours_worked'];
		$aid = $_GET['aid'];

		$stmt = $conn->prepare($query);
		$stmt->bind_param("isssssiii",$cid,$aname,$adescr,$date_assigned,$date_due,$astatus,$grade_impact,$hours_worked,$aid);
		$stmt->execute();

		$success_note .= "The assignment was saved successfully!";
	}
	if($_GET['mode'] == 'delete'){
		$query = "delete from ASSIGNMENT where CID=? and AID=?";

		$cid = $_GET['class'];
		$aid = $_GET['aid'];

		$stmt = $conn->prepare($query);
		$stmt->bind_param("ii",$cid,$aid);
		$stmt->execute();

		$success_note .= "The assignment was deleted successfully!";
	}
	if($_GET['mode']=='new'){
		$query = "select count(*) from ASSIGNMENT where CID="+$_GET['class'];
		$stmt = $conn->prepare($query);
		$stmt->bind_result($num_assignments);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		if($num_assignments > $CREATE_ASSIGNMENT_LIMIT){
			$warning = "Maximum number of assignments has been reached";
		}	else{

			$query = "insert into ASSIGNMENT (CID,ANAME,ADESCR,DATE_ASSIGNED,DATE_DUE,ASTATUS,GRADE_IMPACT,HOURS_WORKED) values (?,?,?,?,?,?,?,?)";

			$cid = $_GET['class'];
			$aname = $_GET['aname'];
			$adescr= $_GET['adescr'];
			$date_assigned = $_GET['date_assigned'];
			$date_due = $_GET['date_due'];
			$astatus = $_GET['status'];
			$grade_impact = $_GET['grade_impact'];
			$hours_worked = $_GET['hours_worked'];

			$stmt = $conn->prepare($query);
			$stmt->bind_param("isssssii",$cid,$aname,$adescr,$date_assigned,$date_due,$astatus,$grade_impact,$hours_worked);
			$stmt->execute();

			$success_note = $aname." was successfully created!";
		}
	}

?>

<html>
	<head>
		<title>View Assignments</title>
		<link rel='stylesheet' style='text/css' href='css/main.css'>
	</head>
	<body>
		<!-- navigate view -->
		<a href='login.php'>Logout</a>&nbsp;<a href='index.php'>Home</a>
		<table>
			<tr>
				<td><a href='<?php echo $la_all; ?>'>All</a></td>
				<td><a href='<?php echo $la_upcoming; ?>'>Upcoming</a></td>
				<td><a href='<?php echo $la_overdue; ?>'>Overdue</a></td>
				<td><a href='<?php echo $la_complete;?>' >Complete</a></td>
				<td><a href='<?php echo $la_hidden; ?>'>Hidden</a></td>
			</tr>
		</table>

		<div class='success_note'><?php echo $success_note; ?></div>

		<!-- select all assignments view -->
		<h1> View <?php echo $_GET['select']; ?> Assignments </h1>
		<table>
			<th class='table_header'>Class</th><th class='table_header'>Assignment</th><th class='table_header'>Description</th><th class='table_header'>Date Assigned</th><th class='table_header'>Date Due</th><th class='table_header'>Grade Impact</th><th class='table_header'>Hours Worked</th><th class='table_header'>Status</th><th class='table_header'>edit</th><th class='table_header'>Hide</th></tr>

			<?php
				$cid_specified = '';
				if($_GET['cid']<>''){
					$cid_specified = " and CID=".$_GET['cid']." ";
					$_SESSION['cid'] = $_GET['cid'];
				}

				if($_GET['select'] == 'all'){
					if($cid_specified=='') $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
					else $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' ".$cid_specified." and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
				}elseif($_GET['select'] == 'upcoming'){
					if($cid_specified=='') $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' and DATE_DUE >= NOW() and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
					else $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' and DATE_DUE >= NOW() ".$cid_specified." and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
				}elseif($_GET['select'] == 'overdue'){
					if($cid_specified=='') $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' and DATE_DUE < NOW() and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
					else $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=0 and astatus<>'CP' and DATE_DUE < NOW() ".$cid_specified." and cid in (select CID from CLASS where UID=".$_SESSION['user'].") order by DATE_DUE, GRADE_IMPACT desc";
				}elseif($_GET['select'] == 'hidden'){
					if($cid_specified == '') $query = "select * from v_ASSIGNMENT_WITH_CLASS where hide=1 and cid in (select CID from CLASS where UID=".$_SESSION['user'].")";
					else $query = "select * from v_ASSIGNMENT_WITH_CLASS where cid in (select CID from CLASS where UID=".$_SESSION['user'].") and hide=1 ".$cid_specified;
				}elseif($_GET['select'] == 'complete'){
					if($cid_specified =='') $query = "select * from v_ASSIGNMENT_WITH_CLASS where astatus='CP' and cid in (select CID from CLASS where UID=".$_SESSION['user'].")";
					else $query = "select * from v_ASSIGNMENT_WITH_CLASS where astatus='CP' and cid in (select CID from CLASS where UID=".$_SESSION['user'].") ".$cid_specified;
				}
			?>

			<?php
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$result = $stmt->get_result();
				$num_rows = $result->num_rows;

				while($row = $result->fetch_assoc()){ ?>
					<?php if(($_GET['mode']<>'edit' or $row['AID']<>$_GET['aid'])){ ?>
					<tr>
						<td><?php echo $row['CNAME']; ?></td>
						<td><?php echo $row['ANAME']; ?></td>
						<td><?php echo $row['ADESCR']; ?></td>
						<td><?php echo $row['DATE_ASSIGNED']; ?></td>
						<td><?php echo $row['DATE_DUE']; ?></td>
						<td><?php echo $row['GRADE_IMPACT']; ?></td>
						<td><?php echo $row['HOURS_WORKED']; ?></td>
						<td><?php echo $row['ASTATUS']; ?></td>
						<td><input type='button' value='edit' onclick='javascript: window.location.href = "<?php echo la_edit($row['AID'],$_GET['select']); ?>";' /></td>

						<?php if( $_GET['select'] != 'hidden'){ ?>
						<td><input type='button' value='hide' onclick='javascript: window.location.href = "<?php echo la_hide($row['AID'],$_GET['select']); ?>";' /></td>
						<?php }else{ ?>
						<td><input type='button' value='unhide' onclick='javascript: window.location.href = "<?php echo la_unhide($row['AID'],$_GET['select']); ?>";' /></td>
						<?php } ?>

					</tr>


							<!-- edit assignments -->
							<?php }else if($_GET['mode'] == 'edit' && $row['AID']==$_GET['aid']){ ?>
									<form method='get' action='view_assignments.php'>
										<tr>
											<input type='hidden' name='cid' value='<?php echo $_GET['cid']; ?>' />
											<input type='hidden' name='select' value='<?php echo $_GET['select']; ?>' />
											<td><?php select_class($conn,$row['CNAME']); ?></td>
											<td><?php text_assignment($row['ANAME']); ?></td>
											<td><?php text_descr($row['ADESCR']); ?></td>
											<td><?php date_dassigned($row['DATE_ASSIGNED']); ?></td>
											<td><?php date_ddue($row['DATE_DUE']); ?></td>
											<td><?php number_gi($row['GRADE_IMPACT']); ?></td>
											<td><?php number_hw($row['HOURS_WORKED']); ?></td>
											<td><?php select_status($row['ASTATUS']); ?></td>
											<input type='hidden' name='aid' value='<?php echo $_GET['aid']; ?>' />
											<td><input type='submit' name='mode' value='save' /></td>
											<td><input type='submit' name='mode' value='delete'/></td>
										</tr>
									</form>
							<?php } ?>
				<?php } ?>

					<tr>
						<form method='get' action='view_assignments.php'>
						<input type='hidden' name='cid' value='<?php echo $_GET['cid']; ?>' />
						<input type='hidden' name='select' value='<?php echo $_GET['select']; ?>' />
						<td><?php select_class($conn,""); ?></td>
						<td><?php text_assignment(""); ?></td>
						<td><?php text_descr(""); ?></td>
						<td><?php date_dassigned(""); ?></td>
						<td><?php date_ddue(""); ?></td>
						<td><?php number_gi(""); ?></td>
						<td><?php number_hw(""); ?></td>
						<td><?php select_status(""); ?></td>
						<td><input type='submit' name='mode' value='new'/></td>
						</form>
					</tr>


		</table>

	</body>
	<footer>
	</footer>
</html>

<?php }else{ header("location: login.php"); } ?>
