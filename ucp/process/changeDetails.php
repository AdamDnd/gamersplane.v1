<?
	$avatarExt = '';
	$fileUploaded = false;
	
	if (isset($_POST['submit'])) {
		if ($_POST['deleteAvatar']) unlink(FILEROOT."/ucp/avatars/{$currentUser->userID}.jpg");
		if ($_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 15 && $_FILES['avatar']['size'] < 1048576) {
			$avatarExt = trim(end(explode('.', strtolower($_FILES['avatar']['name']))));
			if ($avatarExt == 'jpeg') $avatarExt = 'jpg';
			if (in_array($avatarExt, array('jpg', 'gif', 'png'))) {
				$maxWidth = 150;
				$maxHeight = 150;
				
				list($imgWidth, $imgHeight, $imgType) = getimagesize($_FILES['avatar']['tmp_name']);
				if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') $tempImg = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
				elseif (image_type_to_mime_type($imgType) == 'image/gif') $tempImg = imagecreatefromgif($_FILES['avatar']['tmp_name']);
				elseif (image_type_to_mime_type($imgType) == 'image/png') $tempImg = imagecreatefrompng($_FILES['avatar']['tmp_name']);
				
				$xRatio = $maxWidth / $imgWidth;
				$yRatio = $maxHeight / $imgHeight;
				
				if ($imgWidth <= $maxWidth && $imgHeight <= $maxHeight) {
					$finalWidth = $imgWidth;
					$finalHeight = $imgHeight;
				} elseif (($xRatio * $imgHeight) < $maxHeight) {
					$finalWidth = $maxWidth;
					$finalHeight = ceil($xRatio * $imgHeight);
				} else {
					$finalWidth = ceil($yRatio * $imgWidth);
					$finalHeight = $maxHeight;
				}
				
				$tempColor = imagecreatetruecolor($finalWidth, $finalHeight);
				imagealphablending($tempColor, false);
				imagesavealpha($tempColor,true);
				imagecopyresampled($tempColor, $tempImg, 0, 0, 0, 0, $finalWidth, $finalHeight, $imgWidth, $imgHeight);
				
				$destination = FILEROOT.'/ucp/avatars/'.$currentUser->userID.'.'.$avatarExt;
				foreach (glob(FILEROOT.'/ucp/avatars/'.$currentUser->userID.'.*') as $oldFile) unlink($oldFile);
				if ($avatarExt == 'jpg') imagejpeg($tempColor, $destination, 100);
				elseif ($avatarExt == 'gif') imagegif($tempColor, $destination);
				elseif ($avatarExt == 'png') imagepng($tempColor, $destination, 0);
				imagedestroy($tempImg);
				imagedestroy($tempColor);
				$fileUploaded = true;
			} elseif ($avatarExt == 'svg') {
				foreach (glob(FILEROOT.'/ucp/avatars/'.$currentUser->userID.'.*') as $oldFile) unlink($oldFile);
				move_uploaded_file($_FILES['avatar']['tmp_name'], FILEROOT."/ucp/avatars/{$currentUser->userID}.svg");
			}
		}

		$usermeta = array();
		if ($avatarExt == '') $avatarExt = null;
		$currentUser->updateUsermeta('avatarExt', $avatarExt, 1);

		$currentUser->updateUsermeta('showAvatars', isset($_POST['showAvatars'])?1:0);
		if ($_POST['gender'] == 'n') $gender = '';
		else $gender = $_POST['gender'] == 'm'?'m':'f';
		$currentUser->updateUsermeta('gender', $gender);
		$birthday = intval($_POST['year']).'-'.(intval($_POST['month']) <= 9?'0':'').intval($_POST['month']).'-'.(intval($_POST['day']) <= 9?'0':'').intval($_POST['day']);
		if (preg_match('/^[12]\d{3}-[01]\d-[0-3]\d$/', $birthday)) $currentUser->updateUsermeta('birthday', $birthday);
		$currentUser->updateUsermeta('showAge', isset($_POST['showAge'])?1:0);
		$currentUser->updateUsermeta('location', sanitizeString($_POST['location']));
		$currentUser->updateUsermeta('twitter', sanitizeString($_POST['twitter']));
		$currentUser->updateUsermeta('stream', sanitizeString($_POST['stream']));
		$currentUser->updateUsermeta('games', sanitizeString($_POST['games']));
		$currentUser->updateUsermeta('newGameMail', intval($_POST['newGameMail'])?1:0);

		header('Location: /ucp/cp/?updated=1');
	} else header('Location: /user');
?>