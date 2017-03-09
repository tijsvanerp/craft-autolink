<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * Auto Link Twig Extension
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class AutoLinkTwigExtension extends \Twig_Extension
{

    /** @var AutoLinkService */
    protected $autoLink;

    public function __construct()
    {
        $this->autoLink = craft()->autoLink;
    }
    
    /**
     * @return string The extension name
     */
    public function getName()
    {
        return 'AutoLink';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'autoLink' => new \Twig_Filter_Method($this, 'parseInputAndAddLinks'),
        );
    }

    /**
    * @return array
     */
    public function getFunctions()
    {
        return array(
            'autoLink' => new \Twig_Function_Method($this, 'parseInputAndAddLinks'),
        );
    }

    private function canBeCastToString($value) {

        if(is_string($value)) return true;
        if(is_object($value) && method_exists($value, '__toString')) return true;
        if (is_null($value)) return true;
        return is_scalar($value);
    }
    /**
     * @return string
     */
    public function parseInputAndAddLinks($text = null)
    {
        if(!$this->canBeCastToString($text)) {
            return $text;
        }

        $replacements = $this->autoLink->getReplacements();
        return  $this->autoLink->parse((string) $text, $replacements);
    }
}