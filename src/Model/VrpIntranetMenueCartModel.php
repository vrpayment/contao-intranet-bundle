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
    public static function add(array $values)
    {
        $m = new self();
        $m->tstamp = time();
        $m->member = $values['member'];
        $m->type = 'cart';
        $m->items = $values['items'];

        $m->save();

        return $m->current();
    }

}
