<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * AutoLink Model
 *
 * --snip--
 * Models are containers for data. Just about every time information is passed between services, controllers, and
 * templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * https://craftcms.com/docs/plugins/working-with-elements
 * --snip--
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

class AutoLinkModel extends BaseElementModel
{

    protected $elementType = 'AutoLink';

    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(
            parent::defineAttributes(),
            AutoLinkRecord::autoLinkAttributes()
        );
    }

    public function getRedirectEntry()
    {
        if ($this->entryId) {
            return craft()->entries->getEntryById($this->entryId);
        }

        return null;
    }

    public function getUrl()
    {
        if($this->customUrl) {
            return $this->customUrl;
        }
        if($link = craft()->autoLink->getAutoLinkLink($this)) {
            return $link->getUrl();
        }
    }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * Returns the element's CP edit URL.
     *
     * @return string|false
     */
    public function getCpEditUrl()
    {
        return UrlHelper::getCpUrl('autolink/edit/' . $this->id);
    }
}