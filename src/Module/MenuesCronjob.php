<?php


namespace Vrpayment\ContaoIntranetBundle\Module;


use Contao\BackendTemplate;
use Contao\Input;
use Contao\MemberModel;

use Contao\System;
use NotificationCenter\Model\Notification;
use Patchwork\Utf8;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueCartModel;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel;
use Vrpayment\ContaoIntranetBundle\StaticHelper;

class MenuesCronjob extends AbstractModule
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

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['vrp_menuescronjob'][0]) . ' ###';
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
        $member = MemberModel::findOneBy('id', $this->vrp_selectAdmin);

        /** @var Notification $notification */
        $notification = Notification::findByIdOrAlias($this->vrp_selectNotificationAdmin);

        if (null === $notification) {
            return false;
        }

        $dailyOrders = VrpIntranetMenueCartModel::findByDayOrdered(StaticHelper::getLastDayOrderedFor());

        if(null === $dailyOrders)
        {
            return;
        }

        $orders = StaticHelper::getOrdersWithDetails($dailyOrders);

        $ordertext = '';

        $menues = [];

        foreach($orders as $order)
        {
            foreach($order['items'] as $item)
            {
                if(!isset($menues[$item['id']]))
                {
                    $menues[$item['id']] = 1;
                } else {
                    $menues[$item['id']] = $menues[$item['id']]+1;
                }
            }
        }

        foreach($menues as $key => $value)
        {
            $ordertext .=VrpIntranetMenueModel::findOneBy('id', $key)->title.': '.$value.' mal | ';
        }

        // Write Export File
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $folderMenueExports = '/files/intranet-menue-exports';
        $fileName = 'bestellung-'.date('d-m-Y-H-i', StaticHelper::getLastDayOrderedFor()).'.xlsx';

        if(!is_dir($rootDir.$folderMenueExports))
        {
            mkdir($rootDir.$folderMenueExports);
        }

        $excelFilepath =  $rootDir .$folderMenueExports. '/'.$fileName;
        $this->generateExport($menues, $excelFilepath);

        $tokens['admin_mail'] = $member->email;
        $tokens['admin_name'] = $member->firstname.' '.$member->lastname;
        $tokens['orders'] = $ordertext;
        $tokens['orderdate'] = date('d.m.Y H:i', StaticHelper::getLastDayOrderedFor());
        $tokens['exportfile'] = $folderMenueExports. '/'.$fileName;

        $notification->send($tokens);

    }
}
