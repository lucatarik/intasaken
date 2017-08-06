<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once 'common.plugin.inc';
set_time_limit(0);
$url = "http://rld.keyhosting.org/cerco/lista.php?func=1";
$url2 = "http://rem.keyhosting.org/copia/lista.php?func=1";
$debug = !empty($_REQUEST["debug"]);
if ($debug)
    ini_set("display_errors", 1);

class SerialTv extends ListFetcher {

    function update() {
        try {
            ini_set('pcre.backtrack_limit', -1);
            $doc = new DOMDocument();
            $doc->loadHTML($this->fetchedData);

            $xpath = new DOMXpath($doc);

            $elements = $xpath->query("*//tr");
            foreach ($elements as $num => $node) {
                //echo $child->textContent, PHP_EOL;
                if ($num > 0) {
                    $fields = "";
                    $field_el = $xpath->query('(th|td)', $node);
                    $fields .= $field_el->item(1)->textContent . "    " . $field_el->item(0)->textContent . " " . $field_el->item(2)->textContent . " " . $field_el->item(4)->textContent;
                    $fields = trim($fields) . "\r\n";
                    $this->output.=$fields;
                }
            }
        } catch (Exception $ex) {
            var_dump($ex);
        }
    }

}



$scape = new SerialTv($url);
$scape->fetch();
$scape->process();
$scape2 = new SerialTv($url2);
$scape2->fetch();
$scape2->process();
if (!$debug)
    ob_get_clean();
$scape->printout();
$scape2->printout();