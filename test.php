<?php
require_once __DIR__.'/src/Dappurware/SayHello.php';

$instance = new SmartPage\Dappurware\SayHello('Smart Page Extensions: ');
$instance->say("Hello World, this is the greatest Dappur Extensions.");
print_r($instance->read());



require_once __DIR__.'/src/RandomQuotes.php';
// Creates a new object of RandomQuotes class.
$rq = new SmartPage\RandomQuotes();
echo "<br>";
// Generates a random quote.
print_r( $rq->generate() );
echo "\n";
