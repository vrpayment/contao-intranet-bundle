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
    public static function add(int $memberId, array $items)
    {
        $m = new self();
        $m->tstamp = time();
        $m->member = $memberId;
        $m->type = 'cart';
        $m->items = serialize($items);
        $m->completed = 1;
        $m->token = 'order-'.substr(md5($memberId.time()),0, 28);

        $m->save();

        return $m->current();
    }

}
