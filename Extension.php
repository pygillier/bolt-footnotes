<?php

namespace Bolt\Extension\pygillier\footnotes;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
  
    // Regex for matching tags
    static $regex = '#\[fnote]((?:[^[]|\[(?!/?fnote])|(?R))+)\[/fnote]#';
    static $tplNote = '<sup><a href="#footnote-%1$s" id="footnoteref-%1$s">%1$s</a></sup>';
    static $tplRef = '<li id="footnote-%1$s">%2$s&nbsp;<a href="#footnoteref-%1$s">&uarr;</a></li>';

    public function initialize() {
        // Twig filter
        $this->addTwigFilter('footnotes', 'footnotes');
    }

    public function getName()
    {
        return "footnotes";
    }
    
    public function footnotes($content) {
        $notes = array();

        $html = preg_replace_callback(
            self::$regex,  // pattern
            function($match) use(&$notes) { // inner function
                $notes[] = $match[1]; // Get only matched content
                return sprintf(self::$tplNote, count($notes));
            },
            $content);
        
        // iterates over notes and add them to end of post.
        $html.="<h6>Notes</h6>";
        $html.=self::array2ul($notes);

        return new \Twig_Markup($html, 'UTF-8');
    }
    
    public static function array2ul($array) {
        $output = '<ol class="footnotes">';
        foreach ($array as $key => $value) {
            $ptr = $key+1;
            $output .= sprintf(self::$tplRef, $ptr, $value);
        }
        return $output . '</ol>';
    }
}






