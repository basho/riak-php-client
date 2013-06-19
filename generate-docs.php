<?php
function removeNavMenu($file) {
    $dom = new DomDocument('5', 'utf-8');
    $dom->loadHTMLFile($file);

    $xpath = new DomXpath($dom);

    // Remove Nav Menu
    $result = $xpath->query('//ul[@class="nav"]/li/ul');
    if ($result->length > 0) {
        $node = $result->item(0);
        if ($node->childNodes->length == 0) {
            $node->parentNode->parentNode->removeChild($node->parentNode);
        }
    }

    $dom->saveHTMLFile($file);
}

$result = 0;
passthru('cd ' . __DIR__ . ' && phpdoc --force', $result);

if ($result !== 0) {
    die("phpdoc command not found.");
}

libxml_use_internal_errors(true);

$files = new RegexIterator(
    new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./docs')),
    '/^.+\.html$/i',
    RecursiveRegexIterator::GET_MATCH
);

foreach ($files as $file) {
    removeNavMenu($file[0]);
}

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
