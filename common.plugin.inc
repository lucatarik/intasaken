<?php

class ListFetcher {

    public $url;
    public $fetchedData;
    public $output;
    public $curl_err;
    public $mustUpdate;

    function __construct($url) {
        $this->url = $url;
    }

    function get_domain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    function fetch() {
        if (strpos(array_pop(explode('/', $this->url)), '?') !== false)
            $this->url .= "&q=" . (!empty($_REQUEST["q"]) ? urlencode($_REQUEST["q"]) : "");
        else
            $this->url .= "?q=" . (!empty($_REQUEST["q"]) ? urlencode($_REQUEST["q"]) : "");
        $this->fetchedData = $this->curlGet($this->url);
        $this->output = "";
        return $this->fetchedData;
    }

    function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, $this->get_domain($url));
        $curldata = curl_exec($ch);
        $this->curl_err = curl_error($ch);
        curl_close($ch);
        return $curldata;
    }

    function process() {

        $this->update();
    }

    function update() {

    }

    function printout() {

        echo $this->output;
    }

}
