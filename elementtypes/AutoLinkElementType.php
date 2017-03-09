<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * AutoLink ElementType
 *
 * --snip--
 * Element Types are the classes used to identify each of these types of elements in Craft. There’s a
 * “UserElementType”, there’s an “AssetElementType”, and so on. If you’ve ever developed a custom Field Type class
 * before, this should sound familiar. The relationship between an element and an Element Type is the same as that
 * between a field and a Field Type.
 *
 * http://pixelandtonic.com/blog/craft-element-types
 * --snip--
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

class AutoLinkElementType extends BaseElementType
{
    /**
     * Returns this element type's name.
     *
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('AutoLink');
    }

    /**
     * Returns whether this element type has content.
     *
     * @return bool
     */
    public function hasContent()
    {
        return true;
    }

    /**
     * Returns whether this element type has titles.
     *
     * @return bool
     */
    public function hasTitles()
    {
        return true;
    }

    /**
     * Returns whether this element type can have statuses.
     *
     * @return bool
     */
    public function hasStatuses()
    {
        return true;
    }

    /**
     * Returns whether this element type is localized.
     *
     * @return bool
     */
    public function isLocalized()
    {
        return true;
    }

    /**
     * Returns this element type's sources.
     *
     * @param string|null $context
     *
     * @return array|false
     */
    public function getSources($context = null)
    {

        $sources = [
            '*' => [
                'label' => Craft::t('Auto links'),
                'structureEditable' => true,
                'defaultSort' => array('priority', 'asc'),
//                'structureId' => $section->structureId
                ]
        ];

        return $sources;
    }

    /**
     * @inheritDoc IElementType::getAvailableActions()
     *
     * @param string|null $source
     *
     * @return array|null
     */
    public function getAvailableActions($source = null)
    {
        $actions = [];
        $deleteAction = craft()->elements->getAction('Delete');
        $deleteAction->setParams(array(
            'confirmationMessage' => Craft::t('Are you sure you want to delete the selected entries?'),
            'successMessage'      => Craft::t('Entries deleted.'),
        ));
        $actions[] = $deleteAction;

        return $actions;
    }

    /**
     * Returns the attributes that can be shown/sorted by in table views.
     *
     * @param string|null $source
     *
     * @return array
     */
    public function defineTableAttributes($source = null)
    {
        return [
            'title' => Craft::t('Title'),
            'keyphrase' => Craft::t('Keyphrase'),
            'link' => Craft::t('URI'),
            'priority' => Craft::t('Priority')
        ];
    }

    public function defineSortableAttributes()
    {
        return [
            'title' => Craft::t('Title'),
            'keyphrase' => Craft::t('Keyphrase'),
            'priority' => Craft::t('Priority')
        ];
    }


    /**
     * Returns the table view HTML for a given attribute.
     *
     * @param BaseElementModel $element
     * @param string           $attribute
     *
     * @return string
     */
    public function getTableAttributeHtml(BaseElementModel $element, $attribute)
    {
        switch ($attribute) {
            case 'title':
                return $element->title;
                break;
            default:
                return parent::getTableAttributeHtml($element, $attribute);
                break;
        }
    }

    /**
     * Defines any custom element criteria attributes for this element type.
     * @return array
     */
//    public function defineCriteriaAttributes()
//    {
//        return [
//            'locale' => [AttributeType::Locale, 'default' => 'en'],
//            'status' => [AttributeType::String, 'default' => BaseElementModel::ENABLED],
//            'title' => [AttributeType::String, 'default' => '', 'required' => true],
//            'keyphrase' => [AttributeType::String, 'default' => '', 'required' => true]
//        ];
////        return AutoLinkRecord::autoLinkAttributes();
//
//    }

    /**
     * Modifies an element query targeting elements of this type.
     *
     * @param DbCommand            $query
     * @param ElementCriteriaModel $criteria
     *
     * @return mixed
     */
    public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
    {
        $query
            ->addSelect('autolink.*')
            ->join('autolink autolink', 'autolink.id = elements.id')
            ->andWhere(DbHelper::parseParam('autolink.locale', $criteria->locale, $query->params));
    }
    public function onAfterMoveElementInStructure(BaseElementModel $element, $structureId)
    {

    }
    /**
     * Populates an element model based on a query result.
     *
     * @param array $row
     *
     * @return array
     */
    public function populateElementModel($row)
    {
        return AutoLinkModel::populateModel($row);

    }

}