<?
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT c.*, u.username FROM characterLibrary_favorites f, characters c, users u WHERE c.userID = u.userID AND f.userID = {$currentUser->userID} AND c.characterID = {$characterID} AND f.characterID = c.characterID");
	if ($charInfo->rowCount() == 0) { header('Location: /403'); exit }
	$charInfo = $charInfo->fetch();
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Unfavorite Character</h1>
		
		<p class="alignCenter">Are you sure you want to unfavortite <a href="/characters/<?=$charInfo['system']?>/<?=$characterID?>" target="_parent"><?=$charInfo['label']?></a>?</p>
		
		<form method="post" action="/characters/process/favorite/" class="alignCenter">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="unfavorite" class="fancyButton">Unfavorite</button>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>