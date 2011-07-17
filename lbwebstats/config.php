<?php
	error_reporting(0); // Toggle error reporting: 0 = off, E_ALL = on
	$mysqlserver = 'localhost:3306';
	$database = 'minecraft'; //The same as in LB config.
	$tables = array('lb-main'); //List of all tables that should get summed up
	$user = 'mysqlusername';
	$password = 'mysqlpw';
	$cooldown = 10; //Time in seconds between to queries, to prevent spamming of expensive sql queries.
	$excludedPlayers = array('Fire', 'TNT', 'LavaFlow', 'Creeper', 'Ghast', 'LeavesDecay');
	include 'lang_en.php'; //Language file
	include 'materials_en.php'; //Block names file
?>