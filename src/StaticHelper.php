<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */


namespace Vrpayment\ContaoIntranetBundle;


use Contao\StringUtil;
use Model\Collection;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueCartModel;
use Vrpayment\ContaoIntranetBundle\Model\VrpIntranetMenueModel;

class StaticHelper
{
    /**
     * @return false|int
     */
    public static function getDayOrderedFor()
    {
        $now = time();
        $day = date('d', $now);
        $month = date('m', $now);
        $year = date('Y', $now);
        $todayLastOrder = mktime(9, 00, 0, $month, $day, $year);

        if ($todayLastOrder < $now) {
            return strtotime('+1 day', $todayLastOrder);
        }

        return $todayLastOrder;
    }

    /**
     * @return false|int
     */
    public static function getLastDayOrderedFor()
    {
        $now = time();
        $day = date('d', $now);
        $month = date('m', $now);
        $year = date('Y', $now);
        $todayLastOrder = mktime(9, 00, 0, $month, $day+1, $year);

        return $todayLastOrder;
    }

    public static function getOrdersWithDetails(Collection $orders)
    {
        $r = [];

        /** @var VrpIntranetMenueCartModel $order */
        foreach($orders as $order)
        {
            $items = [];

            /** @var VrpIntranetMenueModel $item */
            foreach(StringUtil::deserialize($order->items) as $item)
            {
                $menue = VrpIntranetMenueModel::findOneBy('id', $item);

                if(null === $menue)
                {
                    return null;
                }

                $items[] = [
                    'id' => $menue->id,
                    'title' => $menue->title
                ];
            }

            $member = \MemberModel::findOneBy('id', $order->member);

            $r[] = [
                'id' => $order->id,
                'date' => date('d.m.Y H:i', $order->tstamp),
                'member' => $member->firstname.' '.$member->lastname,
                'items' => $items,
            ];
        }

        return $r;

    }
}
