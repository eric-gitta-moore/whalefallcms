<?php

namespace addons\comment\library;

class Markdown extends Hyperdown
{
    public static function text($text)
    {
        static $parser;
        if (empty($parser)) {
            $parser = new HyperDown();
        }
        $html = $parser->makeHtml($text);
        $index = [];
        $html = preg_replace_callback("/<h(\d+)>(.*?)<\/h\\1>/i", function ($item) use (&$index) {
            $index[$item[1]] = isset($index[$item[1]]) ? $index[$item[1]] + 1 : 1;
            $item[2] = strip_tags($item[2]);
            return "<h{$item[1]} id=\"{$item[2]}-{$index[$item[1]]}\">{$item[2]}</h{$item[1]}>";
        }, $html);
        return $html;
    }

}