<?php
/**
 * Auto Link plugin for Craft CMS
 *
 * AutoLink Controller
 *
 * @author    Tijs van Erp
 * @copyright Copyright (c) 2017 Tijs van Erp
 * @link      http://theconceptstore.nl
 * @package   AutoLink
 * @since     1.0.0
 */

namespace Craft;

/**
 * Class AutoLinkController
 * @package Craft
 */
class AutoLinkController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = [];

    /**
     * @param array $variables
     */
    public function actionEdit(array $variables = [])
    {
        $this->injectLocaleID($variables);
        $variables['autolink'] = !empty($variables['autoLinkId']) ? craft()->autoLink->getAutoLinkOrFail($variables['autoLinkId']) : new AutoLinkModel();
        if(!empty($variables['autolink']['entryId'])) {
            $variables['autoLinkEntry'] = craft()->entries->getEntryById($variables['autolink']['entryId']);
        }
        $variables['entryElementType'] = craft()->elements->getElementType(ElementType::Entry);

        $this->renderTemplate('autolink/_edit', $variables);
    }

    /**
     * @param array $variables
     */
    public function actionLinks(array $variables = [])
    {
        $this->injectLocaleID($variables);
        $variables['attributes']['localeId'] = $variables['localeId'];
        $this->renderTemplate('autolink/index', $variables);
    }

    /**
     * @param $variables
     *
     * @throws HttpException
     */
    protected function injectLocaleID(&$variables)
    {
        $editableLocaleIds = craft()->i18n->getEditableLocaleIds();
        if (isset($variables['localeId'])) {
            // Make sure the user has permission to edit that locale
            if (!in_array($variables['localeId'], $editableLocaleIds)) {
                throw new HttpException(404);
            }
        } else {
            // Are they allowed to edit the current app locale?
            if (in_array(craft()->language, $editableLocaleIds)) {
                $variables['localeId'] = craft()->language;
            } else {
                // Just use the first locale they are allowed to edit
                $variables['localeId'] = $editableLocaleIds[0];
            }
        }
    }

    /**
     * @param array $variables
     */
    public function actionSave(array $variables = [])
    {
        $this->requirePostRequest();

        $autoLinkModel = craft()->autoLink->createAutoLinkModel();

        if (craft()->autoLink->saveAutoLink($autoLinkModel)) {
            craft()->userSession->setNotice(Craft::t('Auto link saved.'));
            $this->redirectToPostedUrl($autoLinkModel);
        } else {
            craft()->userSession->setError(Craft::t('Couldn’t save auto link.'));
            craft()->urlManager->setRouteVariables([
                'autolink' => $autoLinkModel
            ]);
        }
    }


    /**
     * @param $request
     *
     * @return mixed
     */
    protected function locale($request)
    {
        if (!empty($request['localeId'])) return $request['localeId'];
        return craft()->language;
    }

}