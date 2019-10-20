<?php


namespace Vrpayment\ContaoIntranetBundle\Model;

use Contao\Model;

/**
 * Class VrpIntranetMenueCartModel
 *
 * @property int id
 * @property int tstamp
 * @property int member
 * @property string type
 * @property string items
 * @property int completed
 * @property string token
 * @property int orderedFor
 *
 * @package Vrpayment\ContaoIntranetBundle\Model
 */
class VrpIntranetMenueCartModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_vrp_intranet_menue_cart';

    /**
     * @param array $values
     * @return VrpIntranetMenueCartModel
     */
    public static function add(int $memberId, array $items, int $dayOrderedFor)
    {
        $m = new self();
        $m->tstamp = time();
        $m->member = $memberId;
        $m->type = 'cart';
        $m->items = serialize($items);
        $m->completed = 1;
        $m->token = 'order-'.substr(md5($memberId.time()),0, 28);
        $m->orderedFor = $dayOrderedFor;

        $m->save();

        return $m->current();
    }

    public static function findByMemberDayOrdered(int $memberId, int $dayordered)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.member=? AND $t.orderedFor=?"];
        $arrValues[] = $memberId;
        $arrValues[] = $dayordered;

        return static::findBy($arrColumns, $arrValues, array());
    }

    public static function findByDayOrdered(int $dayordered)
    {
        $t = static::$strTable;

        $arrColumns = ["$t.orderedFor=?"];
        $arrValues[] = $dayordered;

        return static::findBy($arrColumns, $arrValues, array());
    }

}
