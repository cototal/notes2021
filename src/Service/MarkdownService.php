<?php

namespace App\Service;

class MarkdownService
{
    public function parse($content)
    {
        $parsedown = new \Parsedown();
        return $parsedown->text($content);
    }
}