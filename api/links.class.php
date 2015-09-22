<?
	class links {
		public $levels = array('Link', 'Affiliate', 'Partner');

		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->get();
			elseif ($pathOptions[0] == 'save') 
				$this->saveLink();
			elseif ($pathOptions[0] == 'deleteImage') 
				$this->deleteImage();
			elseif ($pathOptions[0] == 'deleteLink') 
				$this->deleteLink();
			else 
				displayJSON(array('failed' => true));
		}

		public function get() {
			global $mongo, $currentUser;

			$search = array();
			if (isset($_POST['level'])) {
				if (!is_array($_POST['level'])) 
					$_POST['level'] = array($_POST['level']);
				foreach ($_POST['level'] as $level) 
					if (in_array($level, array('Link', 'Affiliate', 'Partner'))) 
						$search['level'][] = $level;
				if (isset($search['level'])) 
					$search['level'] = array('$in' => $search['level']);
			}
			if (isset($_POST['networks'])) 
				$search['networks'] = $_POST['networks'];
			if (isset($_POST['or'])) {
				$or = array();
				foreach ($search as $key => $value) 
					$or[] = array($key => $value);
				$search = array('$or' => $or);
			}

			$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
			$numLinks = $mongo->links->find($search, array('_id' => 1))->count();
			if (isset($_POST['page'])) 
				$linksResults = $mongo->links->find($search)->sort(array('title' => 1))->skip(PAGINATE_PER_PAGE * ($page - 1))->limit(PAGINATE_PER_PAGE);
			else 
				$linksResults = $mongo->links->find($search)->sort(array('title' => 1));
			$links = array();
			foreach ($linksResults as $rawLink) {
				$link['_id'] = $rawLink['_id']->{'$id'};
				$link['title'] = $rawLink['title'];
				$link['url'] = $rawLink['url'];
				$link['level'] = $rawLink['level'];
				$link['networks'] = is_array($rawLink['networks'])?$rawLink['networks']:array();
				$link['categories'] = is_array($rawLink['categories'])?$rawLink['categories']:array();
				$link['image'] = $rawLink['image'];
				$links[] = $link;
			}
			displayJSON(array('links' => $links, 'totalCount' => $numLinks));
		}

		private function uploadLogo($_id, $logoFile) {
			if ($logoFile['error'] == 0 && $logoFile['size'] > 15 && $logoFile['size'] < 2097152) {
				$logoExt = trim(end(explode('.', strtolower($logoFile['name']))));
				if ($logoExt == 'jpeg') 
					$logoExt = 'jpg';
				if (in_array($logoExt, array('jpg', 'gif', 'png'))) {
					$maxWidth = 300;
					$maxHeight = 300;
					
					list($imgWidth, $imgHeight, $imgType) = getimagesize($logoFile['tmp_name']);
					if ($imgWidth >= $maxWidth || $imgHeight >= $maxHeight) {
						if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') 
							$tempImg = imagecreatefromjpeg($logoFile['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/gif') 
							$tempImg = imagecreatefromgif($logoFile['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/png') 
							$tempImg = imagecreatefrompng($logoFile['tmp_name']);
						
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

						$destination = FILEROOT.'/images/links/'.$_id.'.'.$logoExt;
						foreach (glob(FILEROOT.'/images/links/'.$_id.'.*') as $oldFile) 
							unlink($oldFile);
						if ($logoExt == 'jpg') 
							imagejpeg($tempColor, $destination, 100);
						elseif ($logoExt == 'gif') 
							imagegif($tempColor, $destination);
						elseif ($logoExt == 'png') 
							imagepng($tempColor, $destination, 0);
						imagedestroy($tempImg);
						imagedestroy($tempColor);

						return $logoExt;
					}
				} elseif ($logoExt == 'svg') {
					foreach (glob(FILEROOT.'/images/links/'.$_id.'.*') as $oldFile) 
						unlink($oldFile);
					move_uploaded_file($logoFile['tmp_name'], FILEROOT."/images/links/{$_id}.svg");

					return 'svg';
				}
			}
			return null;
		}

		public function saveLink() {
			global $loggedIn, $mongo;
			if (!$loggedIn) 
				exit;

			$data = array();
			$errors = array();
			if (isset($_POST['_id'])) 
				$data['_id'] = new MongoId($_POST['_id']);
			else
				$data['_id'] = new MongoId();
			$data['title'] = $_POST['title'];
			$data['sortName'] = strtolower($data['title']);
			$data['url'] = $_POST['url'];
			if (!strlen($data['title']) || !strlen($data['url'])) 
				displayJSON(array('failed' => 'incomplete'));
			$data['level'] = $_POST['level'];
			if (!in_array($data['level'], array_keys($this->levels))) 
				$data['level'] = 'Link';
			if (isset($_FILES['file'])) {
				$ext = $this->uploadLogo($data['_id'], $_FILES['file']);
				if ($ext) 
					$data['image'] = $ext;
			}
			$data['networks'] = sizeof($_POST['networks'])?$_POST['networks']:array();
			$data['categories'] = sizeof($_POST['categories'])?$_POST['categories']:array();

			if (!isset($_POST['_id'])) {
				$data['random'] = $mongo->execute('Math.random()');
				$data['random'] = $data['random']['retval'];

				$mongo->links->insert($data);
			} else {
				$mongoID = $data['_id'];
				unset($data['_id']);
				$mongo->links->update(array('_id' => new MongoId($mongoID)), array('$set' => $data));
			}

			displayJSON(array('success' => true, 'image' => $data['image']));
		}

		public function deleteImage() {
			global $loggedIn, $mongo;
			if (!$loggedIn) exit;

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $oldFile) 
				unlink($oldFile);
			$mongo->links->update(array('_id' => new MongoId($_POST['_id'])), array('$unset' => array('image' => '')));
		}

		public function deleteLink() {
			global $loggedIn, $mongo;
			if (!$loggedIn) exit;

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $file) 
				unlink($file);
			$mongo->links->remove(array('_id' => new MongoId($_POST['_id'])));
		}
	}
?>