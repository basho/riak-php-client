<?php
$result = 0;
passthru('cd ' . __DIR__ . ' && phpdoc --force', $result);

if ($result !== 0) {
    die("phpdoc command not found.");
}

libxml_use_internal_errors(true);

$dom = new DomDocument();
$dom->loadHTMLFile("./docs/index.html");

$xpath = new DomXpath($dom);

// Remove "Global" namespace
$result = $xpath->query('//li/a[@href="namespaces/default.html"]');
if ($result->length > 0) {
    $node = $result->item(0);
    $node->parentNode->parentNode->removeChild($node->parentNode);
}

// Remove Packages
$result = $xpath->query('//div[@class="well"][2]');
if ($result->length > 0) {
    $node = $result->item(0);
    $node->parentNode->removeChild($node);
}

$dom->saveHTMLFile('./docs/index.html');
