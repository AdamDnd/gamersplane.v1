<?
	checkLogin(0);
	
	if (isset($_POST['close']) || isset($_POST['open'])) {
		$gameID = intval($_POST['gameID']);
		$gmID = intval($_SESSION['userID']);
		$gmCheck = $mysql->query("SELECT gameID FROM games WHERE gameID = $gameID AND gmID = $gmID");
		
		if ($gmCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} else {
			$mysql->query("UPDATE games SET open = open ^ 1 WHERE gameID = $gameID");
//			$mysql->query('UPDATE characters SET gameID = 0, submittedOn = "0000-00-00 00:00:00" WHERE gameID = '.$gameID);
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT.'/games/my');
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/403');
	}
?>