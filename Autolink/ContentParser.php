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

    /** @var AutoLinkModel[] */
    protected $replacements = [];
    /**
     * @var array
     */
    private $options;

    /**
     * ContentParser constructor.
     *
     * @param string $html
     * @param array  $replacements
     * @param array  $options
     */
    public function __construct(string $html, $replacements = [], $options = [])
    {
        /** @var DOMDocument dom */
        $this->dom = new DOMDocument;
        $this->dom->loadXml("<root>$html</root>");
        $this->options = $options;

        $this->setReplacements($replacements);
    }

    /**
     * Handle the replacements and return the twig string
     * @return Twig_Markup
     */
    public function parse()
    {
        $this->handleReplacements();
        $doc = $this->dom->saveXML($this->dom->documentElement);

        return TemplateHelper::getRaw($this->getContentOfRootElement($doc));
    }


    /**
     * inject the links into the dom model
     * @param AutoLinkModel $autoLinkModel
     */
    protected function replace(AutoLinkModel $autoLinkModel)
    {
        if(!$autoLinkModel->getUrl()) {
            return;
        }

        $nodes = $this->getXPath()->query($this->createQueryExpression($autoLinkModel->getNeedle()));
        foreach ($nodes as $node) {
            /** @var \DOMText $node */
            while (preg_match_all($autoLinkModel->getExpression(), $node->nodeValue, $matches)) {
                $node = $this->injectAutoLinksIntoDom($autoLinkModel, $matches[0][0], $node);
            }
        }
    }

    /**
     * Generate the query expression for querying the dom.
     * The in the settings excluded tags are ignored here
     * @param string $needle
     *
     * @return string
     */
    protected function createQueryExpression($needle) {

        $allowedTags = craft()->autoLink->allowedTags();

        $include = array_map(function($tag) {
            return "ancestor::$tag";
        },$allowedTags);

       return  "//text()[contains(php:functionString('strtolower', .), '$needle')][".implode(" or ", $include) . "]";
    }

    /**
     * strip the root element from the output string. This element is required to make the HTML DomDocument compatible.
     * @param string $doc
     *
     * @return mixed
     */
    protected function getContentOfRootElement($doc)
    {
        preg_match('#<(root)>(.+?)</\1>#is', $doc, $matches);
        return $matches[0];
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
            $word = $node->splitText(stripos($node->nodeValue, $match));
        } else {
            $word = $node->splitText(strpos($node->nodeValue, $match));
        }
        $newNode = $word->splitText(strlen($match));

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
