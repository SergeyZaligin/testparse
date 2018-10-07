<?php

/**
 * Base class CURL
 */
class Curl
{
    /**
     * Instance curl
     * 
     * @var object Curl 
     * 
     */
    private $ch;
    /**
     * Base path url without slash /
     * 
     * @var string
     */
    private $host;
    /**
     * Array with settings curl
     * 
     * @var array
     */
    private $options = [];

    /**
     * 
     * @param type $host
     * @return \self
     */
    public static function app($host)
    {
        return new self($host);
    }

    /**
     * Constructor CURL
     * 
     * @param string $host
     */
    private function __construct($host)
    {
        $this->ch = curl_init();
        $this->host = $host;
        $this->options = [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => []];
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }
    
    /**
     * Destruct instance curl
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * Set option curl and write in array options
     * 
     * @param mixed $name constant
     * @param mixed $value value constant
     * @return $this instance curl
     */
    public function set($name, $value)
    {
            $this->options[$name] = $value;
            curl_setopt($this->ch, $name, $value);
            return $this;
    }

    /**
     * Show current option
     * 
     * @param mixed $name
     * @return mixed
     */
    public function get($name) 
    {
        return $this->options[$name];
    }

    /**
     * Set settings cookie
     * 
     * @param string $path file
     * @return $this instance curl
     */
    public function cookie(string $path): Curl
    {
        $this->set(CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'] . '/' . $path);
        $this->set(CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'] . '/' . $path);
        
        return $this;
    }

    /**
     * Set settings https
     * 
     * @param ints $act 1 - success, $act 0 - no https
     * @return \Curl
     */
    public function ssl($act): Curl
    {
        $this->set(CURLOPT_SSL_VERIFYPEER, $act);
        $this->set(CURLOPT_SSL_VERIFYHOST, $act);
        
        return $this;
    }

    /**
     * Set settings headers
     * 
     * @param int $act
     * @return \Curl
     */
    public function headers($act): Curl
    {
        $this->set(CURLOPT_HEADER, $act);

        return $this;
    }

    /**
     * Set go to the redirect
     * 
     * @param type $param
     * @return \Curl
     */
    public function follow($param): Curl 
    {
        $this->set(CURLOPT_FOLLOWLOCATION, $param);
        
        return $this;
    }

    /**
     * 
     * @param type $url
     * @return \Curl
     */
    public function referer($url): Curl
    {
        $this->set(CURLOPT_REFERER, $url);
        
        return $this;
    }

    /**
     * 
     * @param type $agent
     * @return \Curl
     */
    public function agent($agent): Curl 
    {
        $this->set(CURLOPT_USERAGENT, $agent);
        
        return $this;
    }

    /**
     * 
     * @param type $data
     * @return \Curl
     */
    public function post($data): Curl
    {
        if ($data === false) {
            $this->set(CURLOPT_POST, false);
            
            return $this;
        }

        $this->set(CURLOPT_POST, true);
        $this->set(CURLOPT_POSTFIELDS, http_build_query($data));
        
        return $this;
    }

    /**
     * 
     * @param type $header
     * @return \Curl
     */
    public function add_header($header): Curl
    {
        $this->options[CURLOPT_HTTPHEADER][] = $header;
        $this->set(CURLOPT_HTTPHEADER, $this->options[CURLOPT_HTTPHEADER]);
        
        return $this;
    }

    /**
     * 
     * @param type $headers
     * @return \Curl
     */
    public function add_headers($headers): Curl
    {
        foreach($headers as $h){
            $this->options[CURLOPT_HTTPHEADER][] = $h;
        }
            
        $this->set(CURLOPT_HTTPHEADER, $this->options[CURLOPT_HTTPHEADER]);
        
        return $this;
    }

    /**
     * 
     * @return \Curl
     */
    public function clear_headers(): Curl
    {
        $this->options[CURLOPT_HTTPHEADER] = [];	
        $this->set(CURLOPT_HTTPHEADER, $this->options[CURLOPT_HTTPHEADER]);
        
        return $this;
    }

    /**
     * 
     * @param type $file
     * @return \Curl
     */
    public function config_load($file): Curl
    {
        $data = file_get_contents($file);
        $data = unserialize($data);

        curl_setopt_array($this->ch, $data);

        foreach ($data as $key => $val) {
            $this->options[$key] = $val;
        }

        return $this;
    }

    /**
     * 
     * @param type $file
     * @return \Curl
     */
    public function config_save($file): Curl
    {
            $data = serialize($this->options);
            file_put_contents($file, $data);
            
            return $this;
    }

    /**
     * 
     * @param type $url
     * @return type
     */
    public function request($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->make_url($url));
        $data = curl_exec($this->ch);

        return $this->process_result($data);
    }

    /**
     * 
     * @param string $url
     * @return type
     */
    private function make_url($url)
    {
        if ($url[0] != '/') {
            $url = '/' . $url;
        }
            
        return $this->host . $url;
    }

    /**
     * Headers restrict body
     * 
     * @param mixed $data
     * @return array
     */
    private function process_result($data): array
    {
        
        if (!isset($this->options[CURLOPT_HEADER]) || !$this->options[CURLOPT_HEADER]) {
            return [
                'headers' => [],
                'html' => $data
            ];
        }

        $info = curl_getinfo($this->ch);

        $headers_part = trim(substr($data, 0, $info['header_size']));
        $body_part = substr($data, $info['header_size']);

        $headers_part = str_replace("\r\n", "\n", $headers_part);
        $headers = str_replace("\r", "\n", $headers_part);

        $headers = explode("\n\n", $headers);
        $headers_part = end($headers);

        $lines = explode("\n", $headers_part);
        $headers = [];

        $headers['start'] = $lines[0];

        for ($i = 1; $i < count($lines); $i++) {
                $del_pos = strpos($lines[$i], ':');
                $name = substr($lines[$i], 0, $del_pos);
                $value = substr($lines[$i], $del_pos + 2);
                $headers[$name] = $value;
        }

        return [
                'headers' => $headers,
                'html' => $body_part
        ];
    }
}