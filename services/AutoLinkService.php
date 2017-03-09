<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * AutoLink Service
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

/**
 * Class AutoLinkService
 * @package Craft
 */
/**
 * Class AutoLinkService
 * @package Craft
 */
/**
 * Class AutoLinkService
 * @package Craft
 */
/**
 * Class AutoLinkService
 * @package Craft
 */
class AutoLinkService extends BaseApplicationComponent
{

    /**
     * @return array
     */
    public function getReplacements()
    {
        return [
            'contact' => "/contact",
            'Radiaalpomp' => "/radiaal",
            'koudwater hogedrukreiniger' => '/koudwater-hogedrukreinigers',
            'HogedrukReiniger' => '/hogedrukreinigers',
        ];
    }

    /**
     * @param $html
     * @param $replacements
     *
     * @return \Twig_Markup
     */
    public function parse($html, $replacements)
    {
        $parser = new ContentParser($html, $replacements);

        return $parser->parse();
    }

    /**
     * @return array|mixed
     */
    public function allowedTags()
    {
        return $this->getConfig('allowedTags');
    }

    /**
     * @param null $key
     *
     * @return array|mixed
     */
    public function getConfig($key = null)
    {
        /** @var \Craft\Model $settings */
        $settings = craft()->plugins->getPlugin('autolink')->getSettings();
        if (in_array($key, $settings->attributeNames())) {
            return $settings->getAttribute($key);
        }

        return $settings->getAttributes();
    }

    /**
     * @param AutoLinkModel $autoLinkModel
     *
     * @return EntryModel|null
     */
    public function getAutoLinkLink(AutoLinkModel $autoLinkModel)
    {
        if($autoLinkModel->entryId) {
            return craft()->entries->getEntryById($autoLinkModel->entryId);
        }
        return null;
    }

    /**
     * @return AutoLinkModel
     */
    public function createAutoLinkModel() {
        $autoLinkId = craft()->request->getPost('autoLinkId');
        $autoLinkModel = new AutoLinkModel();

        $entryId = craft()->request->getPost('entryId', $autoLinkModel->entryId);
        $entryId = is_array($entryId) ? array_shift($entryId) : $entryId;

        if ($autoLinkId) {
            $autoLinkModel = craft()->autoLink->getAutoLinkOrFail($autoLinkId);
        }
        $autoLinkModel->getContent()->title = craft()->request->getPost('title');
        $autoLinkModel->getContent()->locale = craft()->request->getPost('localeId', craft()->language);
        $autoLinkModel->priority = craft()->request->getPost('priority', $autoLinkModel->priority);
        $autoLinkModel->locale = craft()->request->getPost('localeId', craft()->language);
        $autoLinkModel->keyphrase = craft()->request->getPost('keyphrase', $autoLinkModel->keyphrase);
        $autoLinkModel->class = craft()->request->getPost('class', $autoLinkModel->class);
        $autoLinkModel->blank = craft()->request->getPost('blank', $autoLinkModel->blank);
        $autoLinkModel->entryId = (int) $entryId;
        $autoLinkModel->useCustomUrl = (bool) craft()->request->getPost('useCustomUrl', $autoLinkModel->useCustomUrl);
        $autoLinkModel->customUrl = craft()->request->getPost('customUrl', $autoLinkModel->customUrl);
        $autoLinkModel->caseSensitive = craft()->request->getPost('caseSensitive', $autoLinkModel->caseSensitive);
        $autoLinkModel->matchWholeWordOnly = craft()->request->getPost('caseSensitive', $autoLinkModel->matchWholeWordOnly);

        return $autoLinkModel;
    }

    /**
     * Get a link by its ID.
     *
     * @param int $id
     *
     * @return AutoLinkModel|null
     */
    public function getLinkById($id)
    {
        $record = AutoLinkRecord::model()->findByAttributes(['id' => $id]);
        return craft()->elements->getElementById($id, 'AutoLink', $record->getAttribute('locale'));
    }

    /**
     * @param AutoLinkModel $autoLinkModel
     *
     * @return bool
     * @throws Exception
     */
    public function saveAutoLink(AutoLinkModel &$autoLinkModel)
    {
        $isNewAutoLink = !$autoLinkModel->id;

        if (!$isNewAutoLink) {
            $autoLinkRecord = AutoLinkRecord::model()->findById($autoLinkModel->id);
            if (!$autoLinkRecord) {
                throw new Exception(Craft::t('No auto link exists with the ID “{id}”',
                    array('id' => $autoLinkModel->id)));
            }
        } else {
            $autoLinkRecord = new AutoLinkRecord();
        }

        $autoLinkRecord->priority = $autoLinkModel->priority;
        $autoLinkRecord->class = $autoLinkModel->class;
        $autoLinkRecord->blank = $autoLinkModel->blank;
        $autoLinkRecord->locale = $autoLinkModel->locale;
        $autoLinkRecord->keyphrase = $autoLinkModel->keyphrase;
        $autoLinkRecord->useCustomUrl = $autoLinkModel->useCustomUrl;
        $autoLinkRecord->customUrl = $autoLinkModel->customUrl;
        $autoLinkRecord->entryId = $autoLinkModel->entryId;
        $autoLinkRecord->caseSensitive = $autoLinkModel->caseSensitive;
        $autoLinkRecord->matchWholeWordOnly = $autoLinkModel->matchWholeWordOnly;

        $autoLinkRecord->validate();
        $autoLinkModel->addErrors($autoLinkRecord->getErrors());
        if ($autoLinkModel->hasErrors()) {
            Craft::dd($autoLinkModel->getErrors());
        }
        if (!$autoLinkModel->hasErrors()) {
            $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
            try {
                $this->onBeforeSaveEvent(new Event($this, array(
                    'event' => $autoLinkModel,
                    'isNewEvent' => $isNewAutoLink
                )));

                if (craft()->elements->saveElement($autoLinkModel, true)) {
                    // Now that we have an element ID, save it on the other stuff
                    if ($isNewAutoLink) {
                        $autoLinkRecord->id = $autoLinkModel->id;
                    }

                    $autoLinkRecord->save(false);

                    // Fire an 'onSaveEvent' event
                    $this->onSaveEvent(new Event($this, array(
                        'event' => $autoLinkModel,
                        'isNewEvent' => $isNewAutoLink
                    )));

                    if ($transaction !== null) {
                        $transaction->commit();
                    }

                    return true;
                }


                Craft::dd($autoLinkModel->getErrors());

            } catch (Exception $e) {
                if ($transaction !== null) {
                    $transaction->rollback();
                }
                throw $e;
            }
        }
    }

    public function getAutoLinkOrFail($autoLinkId)
    {
        $autoLink = craft()->autoLink->getLinkById($autoLinkId);
        if (!$autoLink) {
            throw new Exception(Craft::t('The autolink with the id ({id}) could not be found',
                ['id' => $autoLinkId]));
        }

        return $autoLink;
    }

    /**
     * @param array $attributes
     *
     * @return ElementCriteriaModel
     */
    public function getCriteria(array $attributes = array())
    {
        return craft()->elements->getCriteria(AutoLinkModel::class, $attributes);
    }

    /**
     * Fires an 'onBeforeSaveEvent' event.
     *
     * @param Event $event
     */
    public function onBeforeSaveEvent(Event $event)
    {
        $this->raiseEvent('onBeforeSaveEvent', $event);
    }

    /**
     * Fires an 'onSaveEvent' event.
     *
     * @param Event $event
     */
    public function onSaveEvent(Event $event)
    {
        $this->raiseEvent('onSaveEvent', $event);
    }
}