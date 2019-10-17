<?php


namespace Vrpayment\ContaoIntranetBundle\Model;


use Contao\Model;

/**
 * Class VrpIntranetMenueModel
 *
 * @property int id
 * @property int tstamp
 * @property string title
 * @property string singleSRC
 * @property string published
 *
 * @package Vrpayment\ContaoIntranetBundle\Model
 */
class VrpIntranetMenueModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_vrp_intranet_menue';

    /**
     * @return Model\Collection|VrpIntranetMenueModel|null
     */
    public static function findAllPublished()
    {
        $t = static::$strTable;

        $arrColumns = ["$t.published=?"];
        $arrValues[] = '1';

        return static::findBy($arrColumns, $arrValues, $arrOptions = []);
    }

}
