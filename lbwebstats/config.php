<?php
	$debug = false;
	$mysqlserver = 'localhost:3306';
	$database = 'minecraft'; //The same as in LB config.
	// Change lb-main to be your world name, likely lb-world
	$tables = array('lb-main'); //List of all tables that should get summed up
	$user = 'mysqlusername';
	$password = 'mysqlpw';
	$cooldown = 10; //Time in seconds between to queries, to prevent spamming of expensive sql queries.
	$excludedPlayers = array('Fire', 'TNT', 'LavaFlow', 'Creeper', 'Ghast', 'LeavesDecay', 'WaterFlow', 'SnowForm', 'SnowFade', 'NaturalGrow');
	include 'lang_en.php'; //Language file
	include 'materials_en.php'; //Block names file
?>
