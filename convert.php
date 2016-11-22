#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use \EasyRdf\Graph;
if ($argc == 1) {
	print "Usage: ./conver.php file.csv\n";
	exit(1);
}
$graph = new EasyRdf_Graph();
$level1 = [];
$csv = array_map('str_getcsv', file($argv[1]));
$schema = 'http://joinup.eu/policy-domain';


$graph->addResource($schema, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'skos:ConceptScheme');

foreach ($csv as $nr => $line) {
	if (!$nr) {
		continue;
	}
	// L1 term base properties not written.
	if (!isset($level1[$line[1]])) {
		$graph->addResource($line[1], 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'skos:Concept');
		$graph->addLiteral($line[1], 'skos:prefLabel', $line[0], 'en');
		$graph->addResource($line[1], 'skos:inScheme', $schema);
		$graph->addResource($line[1], 'skos:topConceptOf', $schema);

		$level1[$line[1]] = 1;
	}

	$graph->addResource($line[3], 'rdf:type', 'skos:Concept');
	$graph->addResource($line[3], 'skos:broaderTransitive', $line[1]);
	$graph->addLiteral($line[3], 'skos:prefLabel', $line[2], 'en');
	$graph->addResource($line[3], 'skos:inScheme', $schema);
}
print $graph->serialise('rdfxml');
