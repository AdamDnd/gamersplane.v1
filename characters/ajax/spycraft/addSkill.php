<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['name'])));
			$skillID = $mysql->query('SELECT skillID FROM skillsList WHERE name = "'.$name.'"');
			$stat = sanatizeString($_POST['stat']);
			$statBonus = intval($_POST['statBonus']);
			if ($skillID->rowCount()) $skillID = $skillID->fetchColumn)();
			else {
				$mysql->query('INSERT INTO skillsList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$skillID = $mysql->lastInsertId();
			}
			$addSkill = $mysql->query("INSERT INTO spycraft_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
			if ($addSkill->getResult()) {
				echo "\t\t\t\t\t<div id=\"skill_{$skillID}\" class=\"skill tr clearfix\">\n";
				echo "\t\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($name, MB_CASE_TITLE)."</span>\n";
				echo "\t\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer addStat_{$stat} shortNum\">".showSign($statBonus)."</span>\n";
				echo "\t\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortNum\">".ucwords($stat)."</span>\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skill[{$skillID}][ranks]\" value=\"0\" class=\"skill_ranks shortNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skill[{$skillID}][misc]\" value=\"0\" class=\"skill_misc shortNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillID}][error]\" value=\"\" class=\"skill_error medNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillID}][threat]\" value=\"\" class=\"skill_threat medNum lrBuffer\">\n";
				echo "\t\t\t\t\t\t<input type=\"image\" name=\"skill{$skillID}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillID}\" class=\"skill_remove lrBuffer\">\n";
				echo "\t\t\t\t\t</div>\n";
			}
		}
	}
?>