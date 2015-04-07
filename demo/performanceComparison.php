<?php

$here = dirname(__FILE__);
require_once($here . '/../cli.php');

$dbh = CustomDB::getDBH();
$loops = $argv[1] ?: 1000;
$counter = 0;

// Index hits to minimize lookup time
$sql1 = 'SELECT ID, Name FROM city WHERE Name = ' . $dbh->quote('Kabul') . ' AND CountryCode="AFG"';
$sql2 = 'SELECT ID, Name FROM city WHERE Name = :Name AND CountryCode="AFG"';

// Best case for SQL Statements
$sql1 = "SELECT ID, Name FROM city WHERE ID = 1";
$sql2 = "SELECT ID, Name FROM city WHERE ID = 1";

// Larger statements to try to increase parsing complexity, produced marginal changes
$sql1 = 'SELECT city.ID, city.Name, city.Population, (countrylanguage.Percentage * city.population)/100 AS OfficialSpeakers, countrylanguage.Language AS OfficialLanguage, (100-countrylanguage.Percentage) * (city.population) / 100 AS Nonspeakers, (100-countrylanguage.Percentage) AS PercentNonspeakers FROM city JOIN countrylanguage ON city.CountryCode = countrylanguage.CountryCode AND countrylanguage.IsOfficial = "T" LEFT JOIN city c2 ON city.CountryCode=c2.CountryCode AND city.Population < c2.Population WHERE c2.ID IS NULL AND EXISTS(SELECT 1 FROM countrylanguage cl WHERE countrylanguage.CountryCode = cl.CountryCode AND cl.Language="English" LIMIT 1) ORDER BY city.Population DESC LIMIT 13;';
$sql2 = 'SELECT city.ID, city.Name, city.Population, (countrylanguage.Percentage * city.population)/100 AS OfficialSpeakers, countrylanguage.Language AS OfficialLanguage, (100-countrylanguage.Percentage) * (city.population) / 100 AS Nonspeakers, (100-countrylanguage.Percentage) AS PercentNonspeakers FROM city JOIN countrylanguage ON city.CountryCode = countrylanguage.CountryCode AND countrylanguage.IsOfficial = "T" LEFT JOIN city c2 ON city.CountryCode=c2.CountryCode AND city.Population < c2.Population WHERE c2.ID IS NULL AND EXISTS(SELECT 1 FROM countrylanguage cl WHERE countrylanguage.CountryCode = cl.CountryCode AND cl.Language=:Language LIMIT 1) ORDER BY city.Population DESC LIMIT 13;';

$counter2 = 0;

do {
	$startS = microtime(true);
	do {
		$dbh->query($sql1);
		$counter++;
	} while ($counter < $loops);
	$elapsedS[$counter2] = microtime(true) - $startS;
} while ($counter2++ < $argv[2]);
$totElapsedS = array_sum($elapsedS) / count($elapsedS);

$counter2 = 0;
$counter = 0;
do {
	$startP = microtime(true);
	$pdoStatement = $dbh->prepare($sql2);
	do {
		$pdoStatement->execute(array('Language' => 'English'));
		//$pdoStatement->execute(array('Name' => 'Kabul'));
		$counter++;
	} while ($counter < $loops);
	$elapsedP[$counter2] = microtime(true) - $startP;
} while ($counter2++ < $argv[2]);
$totElapsedP = array_sum($elapsedP) / count($elapsedP);

printf("The elapsed time for the SQL Statements is: %f.\n", $totElapsedS);
printf("The elapsed time for the PrepStatements is: %f.\n", $totElapsedP);
