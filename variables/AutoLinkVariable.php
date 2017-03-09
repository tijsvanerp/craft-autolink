<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * Auto Link Variable
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

class AutoLinkVariable
{

    public function getTags()
    {
        $tags = explode(" ", "a abbr address area article aside b blockquote caption cite code dd del dfn div dl dt em fieldset figcaption footer form h1 h2 h3 h4 h5 h6 header i ins kbd label legend li main nav noscript p pre s samp section small span strong sub sup td template th");
        foreach ($tags as $tag) {
            yield $tag => $tag;
        }
    }
}