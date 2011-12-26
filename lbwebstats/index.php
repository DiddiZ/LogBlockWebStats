<?php session_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>LogBlock WebStats</title>
		<link rel="icon" href="favicon.gif" type="image/x-gif">
		<link rel="stylesheet" href="styles.css" type="text/css">
		<meta name="author" content="DiddiZ">
		<meta name="version" content="v1.2">
		<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
	</head>
	<body>
		<img style="height:200px; margin:10px auto 0; display:block;" src="lb.png">
		<div class="content">
<?php
	include 'config.php';
	$timeSinceLastCall = microtime(true) - $_SESSION['lastquery'];
	if ($cooldown > 0 && $timeSinceLastCall < $cooldown)
		echo '<br><p style="margin:10px;"><b>' . str_replace("{wait}", round($cooldown - $timeSinceLastCall, 2), str_replace("{cooldown}", $cooldown, $msg['spamprevention'])) . '<br><br><a href="?' . $_SERVER['QUERY_STRING'] . '">' . $msg['next'] . '</a></b></p><br>';
	else {
		$verbindung = mysql_connect($mysqlserver, $user, $password) or die("Can't connect to MySQL server!");
		$db_select = @mysql_select_db($database);
		$interval = '';
		if (isset($_GET['lastHour']))
			$interval = 'HOUR';
		elseif (isset($_GET['lastDay']))
			$interval = 'DAY';
		elseif (isset($_GET['lastWeek']))
			$interval = 'WEEK';
		elseif (isset($_GET['lastMonth']))
			$interval = 'MONTH';
		$dateClause = $interval != '' ? "AND date > date_sub(now(), INTERVAL 1 $interval)" : '';
		if (isset($_GET['player'])) {
			$player = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['player']);
			$sql = 'SELECT type, SUM(created) AS created, SUM(destroyed) AS destroyed FROM (';
			for ($i = 0; $i < count($tables); $i++) {
	   			$sql .= "(SELECT type, count(type) AS created, 0 AS destroyed FROM `$tables[$i]` INNER JOIN `lb-players` USING (playerid) WHERE playername = '$player' AND type > 0 AND type != replaced $dateClause GROUP BY type) UNION (SELECT replaced AS type, 0 AS created, count(replaced) AS destroyed FROM `$tables[$i]` INNER JOIN `lb-players` USING (playerid) WHERE playername = '$player' AND replaced > 0 AND type != replaced $dateClause GROUP BY replaced)";
				if ($i < count($tables) - 1)
					$sql .= ' UNION ';
			}
	   		$sql .= ') AS t GROUP BY type ORDER BY SUM(created) + SUM(destroyed) DESC';
	   		$result = mysql_query($sql);
			echo '<table><caption><h1>' . str_replace("{player}", $player, $msg['playerstatstitle']) . '</h1>'
				. '<input type="button" value="' . $msg['alltime'] . '" onclick="location=\'?player=' . $player . '\'">'
				. '<input type="button" value="' . $msg['lasthour'] . '" onclick="location=\'?player=' . $player . '&lastHour=1\'">'
				. '<input type="button" value="' . $msg['lastday'] . '" onclick="location=\'?player=' . $player . '&lastDay=1\'">'
				. '<input type="button" value="' . $msg['lastweek'] . '" onclick="location=\'?player=' . $player . '&lastWeek=1\'">'
				. '<input type="button" value="' . $msg['lastmonth'] . '" onclick="location=\'?player=' . $player . '&lastMonth=1\'"></caption>'
				. '<tr><td></td><td><b>' . $msg['block'] . '</b></td><td><b>' . $msg['created'] . '</b></td><td><b>' . $msg['destroyed'] . '</b></td></tr>';
			if (mysql_num_rows($result) > 0)
				while ($row = mysql_fetch_row($result))
					echo '<tr><td><b>' . ++$counter . '.</b></td><td><img src="blocks/' . $row[0] . '.png"> ' . ($mats[$row[0]] ? $mats[$row[0]] : $row[0]) . '</td><td>' . number_format($row[1], 0, $decimalseparator, $thousandsseparator) . '</td><td>' . number_format($row[2], 0, ',', '.') . '</td></tr>';
			else
				echo "<tr><td><i>$msg[none]</i></td></tr>";
			echo '</table>';
		} else {
			$sql = 'SELECT playername, SUM(created) AS created, SUM(destroyed) AS destroyed FROM (';
			for ($i = 0; $i < count($tables); $i++) {
	   			$sql .= "(SELECT playerid, count(type) AS created, 0 AS destroyed FROM `$tables[$i]` WHERE type > 0 AND type != replaced $dateClause GROUP BY playerid) UNION (SELECT playerid, 0 AS created, count(replaced) AS destroyed FROM `$tables[$i]` WHERE replaced > 0 AND type != replaced $dateClause GROUP BY playerid)";
				if ($i < count($tables) - 1)
					$sql .= ' UNION ';
			}
			$where = '';
			if (count($excludedPlayers) > 0) {
				$where = 'WHERE ';
				for ($i = 0; $i < count($excludedPlayers); $i++) {
					$where .= "playername != '" . $excludedPlayers[$i] . "' ";
					if ($i < count($excludedPlayers) - 1)
						$where .= 'AND ';
				}
			}
	   		$sql .= ') AS t INNER JOIN `lb-players` USING (playerid) ' . $where . 'GROUP BY playerid ORDER BY SUM(created) + SUM(destroyed) DESC';
	   		$result = mysql_query($sql);
			echo '<table><caption><h1>' . $msg['worldstatstitle'] . '</h1>'
				. '<input type="button" value="' . $msg['alltime'] . '" onclick="location=\'?\'">'
				. '<input type="button" value="' . $msg['lasthour'] . '" onclick="location=\'?lastHour=1\'">'
				. '<input type="button" value="' . $msg['lastday'] . '" onclick="location=\'?lastDay=1\'">'
				. '<input type="button" value="' . $msg['lastweek'] . '" onclick="location=\'?lastWeek=1\'">'
				. '<input type="button" value="' . $msg['lastmonth'] . '" onclick="location=\'?lastMonth=1\'"></caption>'
				. '<tr><td></td><td><b>' . $msg['player'] . '</b></td><td><b>' . $msg['created'] . '</b></td><td><b>' . $msg['destroyed'] . '</b></td></tr>';
			if (mysql_num_rows($result) > 0)
				while ($row = mysql_fetch_row($result))
					echo '<tr><td><b>' . ++$counter . '.</b></td><td><a href="?player=' . $row[0] . '"><img src="player.php?' . $row[0] . '">' . ' ' . $row[0] . '</a></td><td>' . number_format($row[1], 0, $decimalseparator, $thousandsseparator) . '</td><td>' . number_format($row[2], 0, ',', '.') . '</td></tr>';
			else
				echo "<tr><td><i>$msg[none]</i></td></tr>";
			echo '</table>';
		}
		echo '<br>';
		if ($cooldown > 0)
			$_SESSION['lastquery'] = microtime(true);
	}
?>
		</div>
		<img style="margin:0 auto; display:block;" src="signpillar.png">
		<div style="position: fixed; right: 0; bottom: 0; font-size: 0.6em;">
			HTML/PHP/CSS by <a href="http://diddiz.insane-architects.net">DiddiZ</a><br>
			LB-Banner by BattleViper<br>
			Block pictures by <a href="http://minecraftwiki.net">minecraftwiki.net</a><br>
			Player skin script by Cadillaxx
		</div>
	</body>
	<script type="text/javascript">
		function callResize() {
			parent.resizeIframe(document.body.scrollHeight);
		}

		window.onload = callResize;
	</script>
</html>
