<?php

$tmpF = "app.db";
$lurl = "http://xdccmule.org/GlobalFindEx/DataBase.db";
$dbg = isset($_REQUEST["debug"]) && $_REQUEST["debug"] ? (true && printf("<pre>")) : false;
$myurl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';

function process_lista($list_raw,$lista_prec = false) {
    global $dbg,$myurl;
    $output = $lista_prec ? $lista_prec : array();
    if ($list_raw == "")
        return $output;
    $dbg ? printf("Scaricata\npeso %s Kbytes\n", round(strlen($list_raw) / 1024, 3)) : 0;
    $lista_arr = explode("\n", $list_raw);
    $dbg ? printf("contiene %s righe\n", count($lista_arr)) : 0;
    $curnet = "";
    foreach ($lista_arr as $lrow) {
        if (preg_match("/^\[(.*?)\]$/", $lrow, $match)) {
            $dbg ? printf("trovato nuovo network: %s\nprecedente: %s\n", $lrow, $curnet) : 0;
            $curnet = trim($lrow);
            if (!isset($output[$curnet]))
                $output[$curnet] = array();
        } else {
            $fields = explode('*', $lrow);
            if (isset($fields[3]) && $fields[3] == "restricted") {
                $channame = array_pop(explode('#', $fields[0]));
                $dbg ? printf("trovato canale con restrizione: %s rete: %s\n", $channame, $curnet) : 0;
                if (file_exists($channame . ".plugin.php")) {
                    $fields[3] = "public";
                    $fields[1] = $myurl . $channame . ".plugin.php";
                    $lrow = implode('*', $fields);
                    $dbg ? printf("lista custom %s trovata\n", $fields[2]) : 0;
                } else {
                    $dbg ? printf("lista custom %s NON trovata\n", $channame) : 0;
                }
            }
            if($lista_prec)
                $output[$curnet][] = count($output[$curnet]) . $lrow;
            else
                $output[$curnet][] = $lrow;
        }
    }
    return $output;
}

if ((isset($_REQUEST["force"]) && $_REQUEST["force"]) || filemtime($tmpF) < strtotime("-12 hours")) {
    $dbg ? printf("Aggiorno lista\n") : 0;
    $list_raw = file_get_contents($lurl);
    if ($list_raw) {

        $output = process_lista($list_raw);
        $output = process_lista(file_get_contents("lista.inc"),$output);
        $dbg ? printf("lista grezza:\n") && print_r($output) : 0;
        $export = "";
        foreach ($output as $key => $value) {
            $export.= $key . "\n" . implode("\n", $value) . "\n";
        }
        if (strlen($export)) {
            echo trim($export)."\n";
            file_put_contents($tmpF, $export);
        }
    } else {
        $dbg ? printf("Impossibile scaricare lista\n") : 0;
        die("12121212\n");
    }
} else {
    echo file_get_contents($tmpF);
}