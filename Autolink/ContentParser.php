<?php

namespace Craft;

use DOMDocument;
use DOMXPath;
use Twig_Markup;

/**
 * Class ContentParser
 * @package Craft
 */
class ContentParser
{
    /**
     * @var DOMDocument
     */
    protected $dom;

    /**
     * @var string
     */
    protected $html;

    /** @var AutoLinkModel[] */
    protected $replacements = [];
    /**
     * @var array
     */
    private $options;

    protected $parsedAutoLinks = 0;

    /**
     * ContentParser constructor.
     *
     * @param string $html
     * @param array  $replacements
     * @param array  $options
     */
    public function __construct(string $html, $replacements = [], $options = [])
    {

        $this->html = (new CleanHTML($html))->clean();

        /** @var DOMDocument dom */
        $this->loadDomString($this->html);
        $this->options = $options;

        $this->setReplacements($replacements);
    }

    /**
     * Load the string into a DOM document
     * @param string $html
     */
    protected function loadDomString(string $html)
    {
        $this->dom = new DOMDocument;
        $this->dom->loadXml("<root>$html</root>");
    }

    /**
     * export the dom coument as a string
     * @return string
     */
    protected function saveDomString()
    {
        $doc = '';
        $nodes = $this->getXPath()->query("/root");
        foreach ($nodes as $node) {
            $doc .= $this->dom->saveXML($node);
        }

        return $doc;
    }

    /**
     * Handle the replacements and return the twig string
     * @return Twig_Markup
     */
    public function parse()
    {
        $this->handleReplacements();

        return TemplateHelper::getRaw($this->saveDomString());
    }


    /**
     * inject the links into the dom model
     *
     * @param AutoLinkModel $autoLinkModel
     */
    protected function replace(AutoLinkModel $autoLinkModel)
    {
        if (!$autoLinkModel->getUrl() || $this->maxAutolinksHaveBeenProcessed() || $this->uRLisCurrentPage($autoLinkModel->getUrl())) {
            return;
        }

        $nodes = $this->getXPath()->query($this->createQueryExpression($autoLinkModel->getNeedle()));
        $hasMatch = false;
        foreach ($nodes as $node) {
            /** @var \DOMText $node */
            while (preg_match_all($autoLinkModel->getExpression(), $node->nodeValue, $matches)) {
                $hasMatch = true;
                $node = $this->injectAutoLinksIntoDom($autoLinkModel, $matches[0][0], $node);
            }
        }
        if ($hasMatch) {
            $this->parsedAutoLinks++;
        }

        $this->loadDomString((string)$this->saveDomString());

    }

    private function uRLisCurrentPage($url)
    {
        $current = craft()->request->getHostInfo() . craft()->request->getRequestUri();
    }

    private function maxAutolinksHaveBeenProcessed()
    {
        return (!empty($this->options['limit']) && $this->options['limit'] == $this->parsedAutoLinks);
    }

    /**
     * Generate the query expression for querying the dom.
     * The in the settings excluded tags are ignored here
     *
     * @param string $needle
     *
     * @return string
     */
    protected function createQueryExpression($needle)
    {

        $allowedTags = craft()->autoLink->allowedTags();

        $include = array_map(function ($tag) {
            return "ancestor::$tag";
        }, $allowedTags);

        return "//text()[contains(php:functionString('strtolower', .), '$needle')][not(ancestor::a) and (" . implode(" or ",
                $include) . ")]";
    }

    /**
     * strip the root element from the output string. This element is required to make the HTML DomDocument compatible.
     *
     * @param string $doc
     *
     * @return mixed
     */
    protected function getContentOfRootElement($doc)
    {
        preg_match('#<(root)>(.+?)</\1>#is', $doc, $matches);

        return $matches[2];
    }

    /**
     * go throught the AutoLinkModels and execute the replacements
     */
    protected function handleReplacements()
    {
        foreach ($this->replacements as $replacement) {
            $this->replace($replacement);
        }
    }


    /**
     * Get the Xpath to query the DOM and bind the PHP functions to perform lowercase matching.
     * @return DOMXPath
     */
    public function getXPath()
    {
        $xPath = new DOMXPath($this->dom);
        $xPath->registerNamespace("php", "http://php.net/xpath");
        $xPath->registerPHPFunctions();

        return $xPath;
    }


    /**
     * Assign the replacements
     *
     * @param ElementCriteriaModel $replacements
     */
    public function setReplacements(ElementCriteriaModel $replacements)
    {
        $this->replacements = $replacements;
    }

    /**
     * @param AutoLinkModel $replacement
     *
     * @return mixed|string
     */
    private function getAutoLinkClassName(AutoLinkModel $replacement)
    {
        return !empty($this->options['class']) ? $replacement->getClassList() . " " . $this->options['class'] : $replacement->getClassList();
    }

    /**
     * @param AutoLinkModel $autoLinkModel
     * @param               $match
     * @param               $node
     *
     * @return \DOMNode
     * @internal param $matches
     */
    protected function injectAutoLinksIntoDom(AutoLinkModel $autoLinkModel, $match, $node)
    {
        /** @var \DOMNode $node */
        if ($autoLinkModel->isCaseSensitive()) {
            $word = $node->splitText(mb_stripos($node->nodeValue, $match));
        } else {
            $word = $node->splitText(mb_strpos($node->nodeValue, $match));
        }
        $newNode = $word->splitText(mb_strlen($match));

        $link = $this->dom->createElement('a');
        $link->setAttribute('href', $autoLinkModel->getUrl());
        $link->setAttribute('title', $autoLinkModel->getTitle());

        if ($autoLinkModel->openInBlankwindow()) {
            $link->setAttribute("target", "_blank");
        }

        $classes = $this->getAutoLinkClassName($autoLinkModel);

        if (strlen($classes) > 0) {
            $link->setAttribute('class', $classes);
        }

        $word->parentNode->replaceChild($link, $word);
        $link->appendChild($word);

        return $newNode;
    }
}
