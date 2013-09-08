<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$updates = array();
			$newSkill = array();
			$skill = array();
			$weapon = array();
			$armor = array();
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_total', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
			$textVals = array('name', 'race', 'class', 'dr', 'items', 'spells', 'notes');
			foreach ($_POST as $key => $value) {
				if ($key == 'alignment') $updates['dnd3_characters`.`alignment'] = in_array($value, array('lg', 'ng', 'cg', 'ln', 'tn', 'cn', 'le', 'ne', 'ce'))?$value:'tn';
				elseif (in_array($key, $textVals)) $updates['dnd3_characters`.`'.$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) $updates['dnd3_characters`.`'.$key] = intval($value);
			}
			
/*			if (strlen($newSkill['name']) >= 3 && $newSkill['name'] != 'Skill Name' && isset($newSkill['stat'])) {
				echo 'New Skill<br>';
				$skillName = sanatizeString(preg_replace('/\s+/', ' ', strtolower($newSkill['name'])));
				$mysql->query('SELECT skillID FROM skillsList WHERE name = "'.$skillName.'"');
				if ($mysql->rowCount()) list($skillID) = $mysql->getList();
				else {
					$mysql->query('INSERT INTO skillsList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
					$skillID = $mysql->lastInsertId();
				}
				$mysql->query("INSERT INTO dnd3_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, {$newSkill['stat']})");
			}*/
			
			if (sizeof($_POST['skills'])) { foreach ($_POST['skills'] as $skillID => $skillInfo) {
				$ranks = intval($skillInfo['ranks']);
				$misc = intval($skillInfo['misc']);
				$mysql->query("UPDATE dnd3_skills SET ranks = $ranks, misc = $misc WHERE characterID = characterID AND skillID = $skillID");
			} }
			
			if (sizeof($_POST['weapons'])) { foreach ($_POST['weapons'] as $weaponKey => $indivWeapon) {
				foreach ($indivWeapon as $key => $value) $indivWeapon[$key] = sanatizeString($value);
				if (strlen($indivWeapon['name']) && strlen($indivWeapon['ab']) && strlen($indivWeapon['damage'])) {
					if (substr($weaponKey, 0, 4) == 'new_') $mysql->query("INSERT INTO dnd3_weapons (characterID, name, ab, damage, critical, `range`, type, size, notes) VALUES ($characterID, '{$indivWeapon['name']}', '{$indivWeapon['ab']}', '{$indivWeapon['damage']}', '{$indivWeapon['critical']}', '{$indivWeapon['range']}', '{$indivWeapon['type']}', '{$indivWeapon['size']}', '{$indivWeapon['notes']}')");
					else $mysql->query("UPDATE dnd3_weapons SET name = '{$indivWeapon['name']}', ab = '{$indivWeapon['ab']}', damage = '{$indivWeapon['damage']}', critical = '{$indivWeapon['critical']}', `range` = '{$indivWeapon['range']}', type = '{$indivWeapon['type']}', size = '{$indivWeapon['size']}', notes = '{$indivWeapon['notes']}' WHERE weaponID = $weaponKey");
				}
			} }
			
			if (sizeof($_POST['armors'])) { foreach ($_POST['armors'] as $armorKey => $indivArmor) {
				foreach ($indivArmor as $key => $value) $indivArmor[$key] = sanatizeString($value);
				if (strlen($indivArmor['name']) && strlen($indivArmor['ac']) && strlen($indivArmor['maxDex'])) {
					if (substr($armorKey, 0, 4) == 'new_') $mysql->query("INSERT INTO dnd3_armors (characterID, name, ac, maxDex, type, `check`, spellFailure, speed, notes) VALUES ($characterID, '{$indivArmor['name']}', '{$indivArmor['ac']}', '{$indivArmor['maxDex']}', '{$indivArmor['type']}', '{$indivArmor['check']}', '{$indivArmor['spellFailure']}', '{$indivArmor['speed']}', '{$indivArmor['notes']}')");
					else $mysql->query("UPDATE dnd3_armors SET name = '{$indivArmor['name']}', ac = '{$indivArmor['ac']}', maxDex = '{$indivArmor['maxDex']}', type = '{$indivArmor['type']}', `check` = '{$indivArmor['check']}', spellFailure = '{$indivArmor['spellFailure']}', speed = '{$indivArmor['speed']}', notes = '{$indivArmor['notes']}' WHERE armorID = $armorKey");
				}
			} }
			
			$mysql->query('UPDATE dnd3_characters, characters SET '.setupUpdates($updates).' WHERE dnd3_characters.characterID = '.$characterID.' AND characters.characterID = dnd3_characters.characterID AND characters.characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		}
		
		header('Location: '.SITEROOT.'/characters/dnd3/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>