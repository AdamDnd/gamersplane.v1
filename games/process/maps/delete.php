<?
	$gameID = intval($_POST['gameID']);
	$mapID = intval($_POST['mapID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, maps m WHERE p.isGM = 1 AND p.gameID = $gameID AND m.gameID = p.gameID AND m.mapID = $mapID AND p.userID = {$currentUser->userID}");
	if (isset($_POST['delete']) && $gmCheck->rowCount()) {
		$mysql->query('UPDATE maps SET deleted = 1 WHERE mapID = '.$mapID);
		echo 1;
	} else echo 0;
?>