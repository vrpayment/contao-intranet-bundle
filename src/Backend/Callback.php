<?php
/**
 * contao-intranet-bundle for Contao Open Source CMS
 *
 * Copyright (C) 2019 47GradNord - Agentur für Internetlösungen
 *
 * @license    commercial
 * @author     Holger Neuner
 */


namespace Vrpayment\ContaoIntranetBundle\Backend;


use Contao\Backend;
use Contao\MemberModel;
use Contao\Model\Collection;
use NotificationCenter\Model\Notification;

class Callback extends Backend
{
    public function getNotifications()
    {
        /** @var Collection $notifications */
        $notifications = Notification::findAll();

        $r = [];

        foreach($notifications as $notification)
        {
            $r[$notification->id] = $notification->title;
        }

        return $r;
    }

    public function getMembers()
    {
        $members = MemberModel::findAll();

        $r = [];

        /** @var MemberModel $member */
        foreach($members as $member)
        {
            $r[$member->id] = $member->firstname.' '.$member->lastname;
        }

        return $r;
    }
}
