<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */


namespace Vrpayment\ContaoIntranetBundle\Module;


use Contao\BackendTemplate;
use Contao\Controller;
use Netzmacht\ContaoFormBundle\Form\FormGeneratorType;
use Patchwork\Utf8;
use Symfony\Component\DependencyInjection\Container;

class MenueList extends AbstractModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_vrp_menuelist';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['vrp-menue-list'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile()
    {
        $formFactory = Controller::getContainer()->get('form.factory');

        $form = $formFactory->create(FormGeneratorType::class, null, ['formId' => 5]);

        dump($form); exit;

    }
}
