<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once 'common.plugin.inc';
set_time_limit(0);
$url = "http://www.theworldofmovies.eu/comments/feed/";
$debug = !empty($_REQUEST["debug"]);
if ($debug)
    ini_set("display_errors", 1);

class SerialTv extends ListFetcher {

    function __construct($url) {
        $data = $this->curlGet($url);
        if ($data && preg_match("/http:\/\/hantarus.info\/(\d+)/", $data, $matches)) {
            $this->url = $matches[0];
        }
    }

    function update() {
        try {
            ini_set('pcre.backtrack_limit', -1);
            if (preg_match_all("/<center><table id=table7(.*?)<center>/ms", $this->fetchedData, $output_array) !== false) {
                foreach ($output_array[0] as $boxnum => $box) {
                    $doc = new DOMDocument();
                    $doc->loadHTML($box);

                    $xpath = new DOMXpath($doc);

                    $elements = $xpath->query("*//center//table[starts-with(@id, 'table')]//tr");
                    foreach ($elements as $num => $node) {
                        //echo $child->textContent, PHP_EOL;
                        if ($num > 0) {
                            $fields = "";
                            $field_el = $xpath->query('(th|td)', $node);
                            $fields .= $field_el->item(1)->textContent . "    " . $field_el->item(0)->textContent . " " . $field_el->item(2)->textContent . " " . $field_el->item(3)->textContent;
                            $fields = trim($fields) . "\r\n";
                            $this->output.=$fields;
                        }
                    }
                }
            } else {
                echo array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
            }
        } catch (Exception $ex) {
            var_dump($ex);
        }
    }

}

$scape = new SerialTv($url);
$scape->fetch();
$scape->process();
if (!$debug)
    ob_get_clean();
$scape->printout();
