<?php

/**
 * Class Parser for native parse
 */
class Parser
{
    /**
     * Cursor 
     * 
     * @var int
     */
    private $cursor;
    
    /**
     * String
     * 
     * @var string
     */
    private $str;
    
    /**
     * 
     * @param string $str
     * @return string
     */
    public static function app($str)
    {
        return new self($str);
    }
    
    /**
     * Constructor class Parser
     * 
     * @param string $str
     */
    private function __construct($str)
    {
        $this->str = $str;
        $this->cursor = 0;
    }
    
    /**
     * Pattern position
     * 
     * @param type $pattern
     * @return boolean
     */
    public function moveTo($pattern)
    {
        $res = strpos($this->str, $pattern, $this->cursor);
        
        if (!$res) {
            return -1;
        }
            
         $this->cursor = $res;
         
         return true;
    }
    
    /**
     * After pattern position
     * 
     * @param type $pattern
     * @return boolean
     */
    public function moveAfter($pattern)
    {
        $res = strpos($this->str, $pattern, $this->cursor);
        
        if (!$res) {
            return -1;
        }
            
         $this->cursor = $res + strlen($pattern);
         
         return true;
    }
    
    /**
     * 
     * @param type $pattern
     * @return type
     */
    public function readTo($pattern)
    {
        $res = strpos($this->str, $pattern, $this->cursor);
        
        if (!$res) {
            return -1;
        }
            
        $out = substr($this->str, $this->cursor, $res - $this->cursor);
        
        $this->cursor = $res;
        
        return $out;
    }
    
   /* 
    
    public function readFrom($pattern){
    
    }
    
    subtag('<table class="infobox', '<table', '</table>')
    
    public function subtag($start, $open, $close){
    
    } */
}