<?php


namespace Vrpayment\ContaoIntranetBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RequestTokenType extends HiddenType
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var string
     */
    private $tokenName;

    /**
     * RequestTokenType constructor.
     *
     * @param CsrfTokenManagerInterface $tokenManager
     * @param string                    $tokenName
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager, string $tokenName)
    {
        $this->tokenManager = $tokenManager;
        $this->tokenName = $tokenName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['full_name'] = 'REQUEST_TOKEN';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data' => $this->tokenManager->getToken($this->tokenName)->getValue(),
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'request_token';
    }

}
