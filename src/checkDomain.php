<?php

namespace Peter\WebsiteInfo;

class CheckDomain
{
    protected string $domain = '';
    protected array $tdls = [];
    protected array $domainInfo = [];

    public function __construct(string $input_domain="")
    {
        $this->domain = $input_domain;
        $jsonData = file_get_contents(__DIR__ . '/json/tld-list.json');
        $this->tdls = json_decode($jsonData);
    }

    /**
     * Trim Domain and return clean base domain
     * 
     * @param string @input_param_domain
     * 
     * @return string @return_domain
     */
    public function trimDomain(string $input_param_domain) : String
    {
        $input_domain = $input_param_domain;
        $input_domain = trim($input_domain);
        if(substr(strtolower($input_domain), 0, 7) == 'http://'){
            $input_domain = substr($input_domain,7);
        }elseif(substr(strtolower($input_domain), 0, 8) == 'https://'){
            $input_domain = substr($input_domain,8);
        }
        
        if(substr(strtolower($input_domain), 0, 4) == 'www.'){
            $input_domain = substr($input_domain,4);
        }
        $return_domain = rtrim($input_domain,'/');
        return $return_domain;
    }

    /**
     * Check Domain TLD - Checking TLD is valid or not
     * 
     * @param string @input_param_domain
     * 
     * @return string TRUE/FALSE
     */
    public function check_tld(string $input_param_domain) : String {
        $input_domain = $input_param_domain;
        $domainParts = explode(".", $input_domain);
        $baseDomain = strtolower($domainParts[0]);
        $inputTLD = strtolower(pathinfo($input_domain, PATHINFO_EXTENSION));
        $tlds = $this->tdls;
        $matched = false;
        foreach ($tlds as $tld) {
            if (isset($tld->domain) && strtolower($tld->domain) === $inputTLD) {
                $matched = true;
                $this->domainInfo[$input_domain]['valid'] = true;
                $this->domainInfo[$input_domain]['base_domain'] = $baseDomain;
                $this->domainInfo[$input_domain]['tld'] = $inputTLD;
                $this->domainInfo[$input_domain]['tld-escricription'] = $tld->description;
                $this->domainInfo[$input_domain]['tdl-type'] = $tld->type;
            }
        }
        if (!$matched) {
            // echo "Input domain does not have a valid TLD: $inputTLD";
            return 'FALSE';
        }else{
            return 'TRUE';
        }
    }

    /**
     * Get Domain Information - Main Function of this class
     *
     * This function retrieves domain information and returns the trimmed domain.
     *
     * @return string $return_domain The trimmed domain
     */
    public function getDomain(): string
    {
        $input_domain = $this->domain;
        $return_domain = '';
        // Trim Domain
        $trim_domain = $this->trimDomain($input_domain);
        // Update Domain Info
        // Update Security Status
        $return_domain = $trim_domain;
        return $return_domain;
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * @return array response
     */
    public function _recaptcha_http_post($host, $path, $data, $port = 80)
    {
        $req = _recaptcha_qsencode($data);
        $proxy_host = "proxy.iiit.ac.in";
        $proxy_port = "8080";
        $http_request = "POST http://{$host}{$path} HTTP/1.0\r\n";
        $http_request .= "Host: {$host}\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;
        $response = '';
        if (false == ($fs = @fsockopen($proxy_host, $proxy_port, $errno, $errstr, 10))) {
            die('Could not open socket aah');
        }
        fwrite($fs, $http_request);
        while (!feof($fs)) {
            $response .= fgets($fs, 1160);
        }
        // One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);
        return $response;
    }
    
    /**
     * http post request
    *
    * @codeCoverageIgnore
    *
    * @param       $url
    * @param array $query
    * @param       $port
    *
    * @return mixed
    */
    public static function sockPost($url, $path = '/', $query = '', $port = 80, $method = 'GET')
    {
        $data = '';
        $fp = fsockopen($url, $port, $errno, $errstr, 30);
        
        if (!$fp) {
            return $data;
        }
        
        // Ensure the method and path are correctly formatted
        $request = $method . ' ' . $path . '?' . $query . ' HTTP/1.0';
        
        $head = $request . "\r\n";
        $head .= 'Host: ' . $url . "\r\n";
        $head .= 'Referer: https://' . $url . $path . "\r\n";
        
        if ($method === 'POST') {
            $head .= "Content-type: application/x-www-form-urlencoded\r\n";
            $head .= 'Content-Length: ' . strlen($query) . "\r\n";
        }
        
        $head .= "\r\n";
        
        if ($method === 'POST') {
            $head .= $query;
        }
        
        $write = fwrite($fp, $head);
        $header = '';
        
        while ($str = trim(fgets($fp, 4096))) {
            $header .= $str;
        }
        
        while (!feof($fp)) {
            $data .= fgets($fp, 4096);
        }
        
        fclose($fp); // Close the socket after use
        
        return $data;
    }
    
    /**
     * Http Curl Request - Try Curl request with input methods
     * 
     * @param   $url
     * @param   $method [GET,POST,etc]
     * @param   $input_path
     * 
     * @return  $respond_data
     */
    public static function curl_request(string $input_url = 'http://www.example.com', string $input_path = "/", string $input_method = 'GET') {
        $ch = curl_init();
        if (!$ch) {
            die("cURL Initiation Failed.");
        }
    
        curl_setopt($ch, CURLOPT_URL, $input_url . $input_path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $return_response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch); // Close the cURL resource
    
        return [
            'response' => $return_response,
            'http_code' => $httpCode,
        ];
    }
    
 
 
}