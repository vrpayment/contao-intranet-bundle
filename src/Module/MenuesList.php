<?php


namespace Vrpayment\ContaoIntranetBundle\Module;


use Contao\BackendTemplate;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\System;
use Haste\Form\Form;
use NotificationCenter\Model\Notification;
use Patchwork\Utf8;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueCartModel;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel;
use Vrpayment\ContaoIntranetBundle\SpreadsheetGenerator;
use Vrpayment\ContaoIntranetBundle\StaticHelper;

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

        $this->sendDailyOverview($this->vrp_selectNotificationAdmin, $this->vrp_selectAdmin);

        $dayOrderedFor = StaticHelper::getDayOrderedFor();

        if('addMenue' === Input::post('FORM_SUBMIT'))
        {
            /** @var VrpIntranetMenueModel $menue */
            $menue = VrpIntranetMenueModel::findOneBy('id', Input::post('item'));

            $order = VrpIntranetMenueCartModel::add($this->User->id, [$menue->id], $dayOrderedFor);

            $this->sendNotificationAfterOrder((int)$this->vrp_selectNotification, $this->User, MemberModel::findOneBy('id', $this->vrp_selectAdmin), $order);

            return $this->redirectToStep('bestellt', 'order='.$order->token);
        }

        $this->Template->part = (null === Input::get('bestellt')) ? 'select' : 'order';
        $this->Template->order = (null === Input::get('bestellt')) ? null : $this->getOrderDetails(VrpIntranetMenueCartModel::findOneBy('token', Input::get('order')));
        $this->Template->menues = $this->getMenueList();
        $this->Template->user = $this->User->getData();
        $this->Template->dayOrderedFor = date('d.m.Y', $dayOrderedFor);
        $this->Template->ordersCurrentDay = $this->getOrdersCurrentDay($this->User, $dayOrderedFor);
    }

    /**
     * @param FrontendUser $user
     * @param int $timestamp
     * @return array|void
     */
    protected function getOrdersCurrentDay(FrontendUser $user, int $timestamp)
    {
        $orders = VrpIntranetMenueCartModel::findByMemberDayOrdered($user->id, $timestamp);

        if(null === $orders)
        {
            return;
        }

        return StaticHelper::getOrdersWithDetails($orders);
    }

    /**
     * @param VrpIntranetMenueCartModel $cartModel
     * @return string|null
     */
    protected function getOrderDetails(VrpIntranetMenueCartModel $cartModel)
    {
        if(null === $cartModel)
        {
            return null;
        }

        $order = '';

        foreach(StringUtil::deserialize($cartModel->items) as $itemId) {
            $menuModel = VrpIntranetMenueModel::findOneBy('id', $itemId);

            if (null === $menuModel)
            {
                continue;
            }

            $order.='- '.$menuModel->title.',';
        }

        return $order;
    }

    protected function sendNotificationAfterOrder(int $notificationId, FrontendUser $user, MemberModel $memberModel, VrpIntranetMenueCartModel $cartModel)
    {
        /** @var Notification $notification */
        $notification = Notification::findByIdOrAlias($notificationId);

        if (null === $notification) {
            return false;
        }

        $order = '';

        foreach(StringUtil::deserialize($cartModel->items) as $itemId) {
            $menuModel = VrpIntranetMenueModel::findOneBy('id', $itemId);

            if (null === $menuModel)
            {
                continue;
            }

            $order.='- '.$menuModel->title.',';
        }

        $tokens['admin_mail'] = $memberModel->email;
        $tokens['admin_name'] = $memberModel->firstname.' '.$memberModel->lastname;
        $tokens['member_email'] = $user->email;
        $tokens['member_name'] = $user->firstname.' '.$user->lastname;
        $tokens['order'] = $order;
        $tokens['orderdate'] = date('d.m.Y H:i', $cartModel->tstamp);

        $notification->send($tokens);
    }

    protected function sendDailyOverview($notificationId, int $adminMemberId)
    {
        if(null !== Input::get('send'))
        {
            $member = MemberModel::findOneBy('id', $adminMemberId);

            /** @var Notification $notification */
            $notification = Notification::findByIdOrAlias($notificationId);

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



    protected function generateExport(array $menues, string $pathToFile)
    {
        /** @var SpreadsheetGenerator $spreadsheet */
        $spreadsheet = new SpreadsheetGenerator(new Spreadsheet());
        $spreadsheet->setSheetRow($this->getSheetFirstRow());
        $count = 2;

        foreach($menues as $key => $value)
        {
            $row = [
                'A'.$count => VrpIntranetMenueModel::findOneBy('id', $key)->title,
                'B'.$count => $value,
            ];

            $spreadsheet->setSheetRow($row);

            $count++;
        }

        $spreadsheet->saveFileOutputXls($pathToFile);
    }

    protected function getSheetFirstRow()
    {
        return [
            'A1' => 'Menü',
            'B1' => 'Anzahl'
        ];

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
