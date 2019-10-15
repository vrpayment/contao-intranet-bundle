<?php


namespace Vrpayment\ContaoIntranetBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenueListController extends AbstractController
{
    /**
     * @param Request $request
     * @param array   $moduleSettings
     *
     * @return Response
     */
    public function mainAction(Request $request, array $moduleSettings): Response
    {
        return $this->render('@ContaoIntranet/module/menueList.html.twig', [
            'tpl' => 'tpl',
        ]);
    }
}
