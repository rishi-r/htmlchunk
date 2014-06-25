<?php

class HtmlChunk{

    private $file_content;
    private $priorty;
    private $newArr = array();
    private $paraCount = 0;
    private $final_content = null;
    function __construct()
    {
        $this->file_content = file_get_contents("new.html");
        $this->priorty = array(
                            'p', 'table' , 'div', 'ul' , 'h1', 'h2' 
                        );
        $this->notBreak = array('table','p','ul','li','h1','h2','h3','h4','h5','h6','blockquote');
        $this->createChunk();
    }

    function createChunk() {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($this->file_content);
        $this->innerNodes($doc);
        $this->completeArr($doc);
    }

    function innerNodes($doc) {
        foreach ($doc->childNodes as $p)
        {
            if($this->isBreak($p))
            {
                if ($this->hasChild($p)) 
                {
                    $this->innerNodes($p);
                } 
                elseif ($p->nodeType == XML_ELEMENT_NODE)
                {
                    $this->assignArr($p);
                }
            }
            else
            {
                $this->assignArr($p);
            }
        }
    }
    
    function isBreak($p)
    {
        if(!in_array(strtolower($p->nodeName), $this->notBreak) && $this->hasChild($p))
        {
            return true;
        }
        else
            return false;
    }
    function hasChild($p) {
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $c) {
                if ($c->nodeType == XML_ELEMENT_NODE)
                    return true;
            }
        }
    }
    
    function assignArr($p)
    {
        $data = $p->ownerDocument->saveHTML($p);
        if(strlen(trim($data)) != 0)
        {
            $this->newArr[$this->paraCount] =  $data;
            $p->nodeValue = "{{".$this->paraCount."}}";
            $this->paraCount++;
        }
    }
    
    function completeArr($doc)
    {
        $content = $doc->saveHTML();
        $this->final_content = preg_replace('(<[^>]+>(\{\{[0-9]+\}\})</[^>]+>)i', '${1}' , $content);
    }
    
    
    function makeOri($content)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($content);
        $this->innerNodes2($doc);
    }
    
    function innerNodes2($doc)
    {
        foreach ($doc->childNodes as $p)
        {
            if ($this->hasChild($p)) 
            {
                $this->innerNodes2($p);
            } 
            else
            {
                print_r($p->ownerDocument->saveHTML());
            }
        }
        
    }
}

$htmlchunk = new HtmlChunk();