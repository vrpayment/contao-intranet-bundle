<?php


namespace Vrpayment\ContaoIntranetBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vrpayment\ContaoIntranetBundle\Form\RequestTokenType;

abstract class AbstractController extends Controller implements MainActionProvidingInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function mainAction(Request $request, array $moduleSettings): Response;

    /**
     * @param string $name
     *
     * @return FormBuilderInterface
     */
    protected function createFormBuilderForContao(string $name): FormBuilderInterface
    {
        return $this->get('form.factory')->createNamedBuilder($name, FormType::class)
            ->add('REQUEST_TOKEN', RequestTokenType::class);
    }
}
