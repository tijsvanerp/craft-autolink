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

/**
 * Class AutoLinkModel
 * @package Craft
 */
class AutoLinkModel extends BaseElementModel
{

    /**
     * @var string
     */
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

    /**
     * @return EntryModel|null
     */
    public function getRedirectEntry()
    {
        if ($this->entryId) {
            return craft()->entries->getEntryById($this->entryId);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        if ($this->customUrl) {
            return $this->customUrl;
        }
        if ($link = craft()->autoLink->getAutoLinkLink($this)) {
            return $link->getUrl();
        }

        return false;
    }

    public function isCaseSensitive()
    {
        return (bool) $this->caseSensitive;
    }

    public function expandMatchToWholeWord()
    {
        return (bool) $this->expandMatchToWholeWord;
    }

    /**
     * @return string
     */
    public function getKeyPhrase()
    {
        return $this->keyphrase;
    }

    /**
     * @return string
     */
    public function getNeedle()
    {
        return strtolower($this->getKeyPhrase());
    }

    public function openInBlankwindow()
    {
        return (bool) $this->blank;
    }


    public function getClassList()
    {
        return $this->class;
    }
    /**
     * @return string
     */
    public function getExpression()
    {
        $expression = '/%s/u';

        if ($this->expandMatchToWholeWord()) {
            $expression = '/\b(\w*%s\w*)\b/';
        }
        $expression = $this->isCaseSensitive() ? $expression : $expression . "i";

        return sprintf($expression, preg_quote($this->getKeyPhrase(), "/"));

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