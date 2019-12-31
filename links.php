<?php session_start(); ?>

<?php
	//list assignments (views)
	$la_all = "view_assignments.php?select=all&cid=".$_SESSION['cid'];
	$la_upcoming = "view_assignments.php?select=upcoming&cid=".$_SESSION['cid'];
	$la_overdue = "view_assignments.php?select=overdue&cid=".$_SESSION['cid'];
	$la_hidden = "view_assignments.php?select=hidden&cid=".$_SESSION['cid'];
	$la_complete = "view_assignments.php?select=complete&cid=".$_SESSION['cid'];

	//edit / hide assignment
	function la_edit($aid,$select){
		return "view_assignments.php?mode=edit&aid=$aid&select=$select&cid=".$_SESSION['cid'];
	}
	function la_hide($aid,$select){
		return "view_assignments.php?mode=hide&aid=$aid&select=$select&cid=".$_SESSION['cid'];
	}
	function la_unhide($aid,$select){
		return "view_assignments.php?mode=unhide&aid=$aid&select=$select&cid=".$_SESSION['cid'];
	}
	function la_schange($aid,$old_status,$select){
		if($old_status == 'NS') return "view_assignments.php?mode=schange&status=IP&aid=$aid&select=$select&cid=".$_SESSION['cid'];	
		if($old_status == 'IP') return "view_assignments.php?mode=schange&status=CP&aid=$aid&select=$select&cid=".$_SESSION['cid'];
		if($old_status == 'CP') return "view_assignments.php?mode=schange&status=NS&aid=$aid&select=$select&cid=".$_SESSION['cid'];
	}
	function next_status($status){
		if($status == 'NS') return 'IP';	
		if($status == 'IP') return 'CP';
		if($status == 'CP') return 'NS';
	}
?>


<?php //list classes
	$lc = "view_class.php";

	function lc_select($cid){
		return "view_assignments.php?select=upcoming&cid=$cid";
	}

?>