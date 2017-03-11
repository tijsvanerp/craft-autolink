<?php

namespace Craft;


use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Class CleanHTML
 * @package Craft
 */
class CleanHTML
{
    /**
     * @var string
     */
    protected $html = '';

    /**
     * CleanHTML constructor.
     *
     * @param string $html
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

    public function clean() {

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', CRAFT_STORAGE_PATH);
        $config->set('Attr.AllowedFrameTargets', ["_blank"]);

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($this->html);

    }

}
