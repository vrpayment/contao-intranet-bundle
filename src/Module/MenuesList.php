<?php


namespace Vrpayment\ContaoIntranetBundle\Module;


use Contao\BackendTemplate;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Model\Collection;
use Haste\Form\Form;
use Patchwork\Utf8;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueCartModel;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel;

class MenuesList extends AbstractModule
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

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['vrp_menueslist'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        Input::setGet('bestellt', Input::get('auto_item'));

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile()
    {
        $this->import(FrontendUser::class, 'User');

        if('addMenue' === Input::post('FORM_SUBMIT'))
        {
            /** @var VrpIntranetMenueModel $menue */
            $menue = VrpIntranetMenueModel::findOneBy('id', Input::post('item'));

            $order = VrpIntranetMenueCartModel::add($this->User->id, [$menue->id]);

            return $this->redirectToStep('bestellt', 'order='.$order->token);

        }

        $this->Template->part = (null === Input::get('bestellt')) ? 'select' : 'order';
        $this->Template->menues = $this->getMenueList();
        $this->Template->user = $this->User->getData();
    }

    protected function getMenueList()
    {
        /** @var Collection $menueCollection */
        $menueCollection = VrpIntranetMenueModel::findAllPublished();

        if(null === $menueCollection)
        {
            return null;
        }

        $r = [];

        /** @var VrpIntranetMenueModel $menu */
        foreach($menueCollection as $menu)
        {
            $r[] = [
                'menue' => $menu->row(),
                'src' => $this->getImageObject($menu->singleSRC, [300,250])
            ];
        }

        return $r;
    }
}
