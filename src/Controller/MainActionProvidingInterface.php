<?php


namespace Vrpayment\ContaoIntranetBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MainActionProvidingInterface
{
    /**
     * @param Request $request
     * @param array   $moduleSettings
     *
     * @return Response
     */
    public function mainAction(Request $request, array $moduleSettings): Response;
}
