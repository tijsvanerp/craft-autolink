<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * AutoLink Record
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

class AutoLinkRecord extends BaseRecord
{

    /**
     * @return string
     */
    public function getTableName()
    {
        return 'autolink';
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return self::autoLinkAttributes();

    }

    /**
     * @return array
     */
    public function defineRelations()
    {
        return array();
    }

    public static function autoLinkAttributes()
    {
        return [
//            'title' => [AttributeType::String, 'default' => '', 'required' => true],
            'keyphrase' => [AttributeType::String, 'default' => '', 'required' => true],
            'locale' => [AttributeType::String, 'default' => null, 'required' => true],
            'entryId' => [AttributeType::Number, 'default' => null],
            'useCustomUrl' => [AttributeType::Bool, 'default' => false],
            'customUrl' => [AttributeType::String, 'default' => null],
            'caseSensitive' => [AttributeType::Bool, 'default' => false],
            'matchWholeWordOnly' => [AttributeType::Bool, 'default' => true],
            'priority' => [AttributeType::Number, 'default' => 0],
            'blank' => [AttributeType::Bool, 'default' => false],
            'class' => [AttributeType::String, 'default' => null],
        ];
    }
}