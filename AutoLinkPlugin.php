<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * Links keywords in your content to other entries
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 * @VERSION 1.0.4
 */

namespace Craft;

class AutoLinkPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
        Craft::import('plugins.autolink.Autolink.*');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('Auto Link');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Links keywords in your content to other entries');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/tijsvanerp/craft-autolink';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/tijsvanerp/craft-autolink/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.4';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Tijs van Erp';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://theconceptstore.nl';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    public function registerCpRoutes()
    {
        return [
            'autolink' => ['action' => 'AutoLink/links'],
            'autolink/new' => ['action' => 'AutoLink/edit'],
            'autolink/delete' => ['action' => 'AutoLink/delete'],
            'autolink/save' => ['action' => 'AutoLink/save'],
            'autolink/edit/(?P<autoLinkId>[-\w\.*]+)' => ['action' => 'AutoLink/edit'],
            'autolink/(?P<localeId>[-\w\.*]+)' => ['action' => 'AutoLink/links'],
        ];
    }

    /**
     * @return mixed
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.autolink.twigextensions.AutoLinkTwigExtension');
        return new AutoLinkTwigExtension();
    }

    /**
     */
    public function onBeforeInstall()
    {
    }

    /**
     */
    public function onAfterInstall()
    {
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    }

    /**
     * @return array
     * a abbr address area article aside b base blockquote body button caption cite code col colgroup data datalist dd
     * del dfn div dl dt em embed fieldset figcaption figure footer form h1 h2 h3 h4 h5 h6 head header hr html i iframe
     * img input ins kbd keygen label legend li link main map mark meta meter nav noscript object ol optgroup option
     * output p param pre progress q rb rp rt rtc ruby s samp script section select small source span strong style sub
     * sup table tbody td template textarea tfoot th thead time title tr track u ul var video wbr
     */
    protected function defineSettings()
    {
        return [
            'allowedTags' => [
                AttributeType::Mixed, 'default' =>
                    [
                        'p', 'abbr', 'address', 'article', 'aside', 'b', 'blockquote', 'caption',
                        'data', 'dd', 'div', 'dl', 'em', 'fieldset', 'figcaption', 'footer', 'form', 'header',
                        'i', 'li', 'main', 'nav', 'noscript', 'pre', 'section', 'small', 'span', 'strong', 'sub', 'sup',
                        'td'
                    ]],
        ];
    }

    /**
     * @return mixed
     */
    public function getSettingsHtml()
    {
        return craft()->templates->render('autolink/AutoLink_Settings', array(
            'settings' => $this->getSettings()
        ));
    }

    /**
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...

        return $settings;
    }
}