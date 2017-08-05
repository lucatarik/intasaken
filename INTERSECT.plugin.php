<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1);
    require_once 'common.plugin.inc';
    set_time_limit(0);
    $url = "http://www.giocopazzo.it/lista/index.php?password=spuprevuji";
    class INTERSECT extends ListFetcher {

        function update() {
            try {
                ini_set('pcre.backtrack_limit', -1);
                if (preg_match_all("/<center>(.*?)<\/center>/ms", $this->fetchedData, $output_array)!==false) {
                    foreach ($output_array[0] as $boxnum=>$box) {
                        $doc = new DOMDocument();
                        $doc->loadHTML($box);

                        $xpath = new DOMXpath($doc);

                        $elements = $xpath->query("*//center//table[starts-with(@id, 'table')]");
                        foreach ($elements as $num => $node) {
                            if ($num > 0) {
                                foreach ($xpath->query('//table[2]//tr', $node) as $child) {
                                    //echo $child->textContent, PHP_EOL;
                                    $fields = "#";
                                    $field_el = $xpath->query('(th|td)', $child);
                                    $fields .= $field_el->item(1)->textContent."    ".$field_el->item(0)->textContent." ".$field_el->item(2)->textContent." ".$field_el->item(3)->textContent;
                                    $fields = trim($fields) . "\r\n";
                                    $this->output.=$fields;
                                }
                            }
                        }
                    }
                }
                else {
                echo array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
                }
            } catch (Exception $ex) {
                var_dump($ex);
            }
        }

    }

    $scape = new INTERSECT($url);
    $scape->fetch();
    $scape->process();
    ob_get_clean();
    $scape->printout();
