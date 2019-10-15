<?php


namespace Vrpayment\ContaoIntranetBundle\Proxys;


use Contao\BackendTemplate;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Module;
use Contao\System;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Response;

class FrontendProxyModule extends Module
{
    /**
     * Generates the module by delegating it to a service.
     *
     * @return string
     */
    public function generate()
    {
        if ('BE' === TL_MODE) {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0] ?: '') . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $container = System::getContainer();
        $request = $container->get('request_stack')->getCurrentRequest();

        $class = 'ContaoIntranetBundle\\Controller\\' . Container::camelize(str_replace('vrpayment_', '', $this->type)) . 'Controller';

        $controller = new $class();

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($container);
        }

        /** @var Response $response */
        $response = $controller->mainAction($request, $this->arrData);

        if (200 === $response->getStatusCode()) {
            return $response->getContent();
        }

        throw new ResponseException($response);
    }

    /**
     * Just to meet the parent class requirements.
     */
    protected function compile()
    {
    }
}
