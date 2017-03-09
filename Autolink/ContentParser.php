<?php

namespace Craft;

use DOMDocument;
use DOMXPath;
use SimpleXMLElement;

class ContentParser
{
    protected $dom;
    protected $replacements = [];

    public function __construct(string $html, $replacements = [])
    {
        /** @var DOMDocument dom */
        $this->dom = new DOMDocument;
        $this->dom->loadXml("<root>$html</root>");

        $this->setReplacements($replacements);
    }

    public function parse()
    {
        $this->handleReplacements();
        $doc = $this->dom->saveXML($this->dom->documentElement);

        return TemplateHelper::getRaw($this->getContentOfRootElement($doc));
    }

    protected function replace($find, $replace)
    {
        $find = strtolower($find);
        $nodes = $this->getXPath()->query($this->createQueryExpression($find));

        foreach ($nodes as $node) {
            /** @var \DOMText $node */
            while (preg_match("/\b$find\b/i", $node->nodeValue)) {
                /** @var \DOMNode $node */
                $word = $node->splitText(stripos($node->nodeValue, $find));
                $after = $word->splitText(strlen($find));

                $link = $this->dom->createElement('a');
                $link->setAttribute('href', $replace);

                $word->parentNode->replaceChild($link, $word);
                $link->appendChild($word);

                $node = $after;
            }
        }

    }

    protected function createQueryExpression($find) {

        $allowedTags = craft()->autoLink->allowedTags();

        $include = array_map(function($tag) {
            return "ancestor::$tag";
        },$allowedTags);

//       return  "//*[contains(text(), '$find')][".implode(" and ", $not) . "]";
       return  "//text()[contains(php:functionString('strtolower', .), '$find')][".implode(" or ", $include) . "]";
    }

    protected function getContentOfRootElement($doc)
    {
        preg_match('#<(root)>(.+?)</\1>#is', $doc, $matches);
        return $matches[0];
    }

    protected function handleReplacements()
    {
        foreach ($this->replacements as $find => $url) {
            $this->replace($find, $url);
        }
    }

    public function getXPath()
    {
        $xPath = new DOMXPath($this->dom);
        $xPath->registerNamespace("php", "http://php.net/xpath");
        $xPath->registerPHPFunctions();

        return $xPath;
    }

    public function addReplacements(array $replacements)
    {
        $this->replacements = array_merge($this->replacements, $replacements);
    }

    public function setReplacements(array $replacements)
    {
        $this->replacements = $replacements;
    }

    public function clearReplacements()
    {
        $this->replacements = [];
    }
}
