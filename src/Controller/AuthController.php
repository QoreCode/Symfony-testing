<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //TODO refactor it
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $loginForm = $this->createFormBuilder(new User(), ['csrf_token_id' => 'authenticate'])
            ->add('login', TextType::class, ['data' => $authenticationUtils->getLastUsername()])
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Send',
                'attr' => ['class' => 'btn waves-effect waves-light']
            ])
            ->getForm();

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $loginForm->addError(new FormError($error->getMessage()));
        }

        return $this->render('auth/login.html.twig', [
            'form' => $loginForm->createView(),
            'error' => $error
        ]);
    }

    public function logout()
    {
        //todo logout action
    }

    public function registration(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        $registrationForm = $this->createFormBuilder(new User(), ['csrf_token_id' => 'registration'])
            ->add('login', TextType::class, ['data' => $authenticationUtils->getLastUsername()])
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('age', IntegerType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Send',
                'attr' => ['class' => 'btn waves-effect waves-light']
            ])
            ->getForm();

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $registrationForm->addError(new FormError($error->getMessage()));
        }

        return $this->render('auth/registration.html.twig', [
            'form' => $registrationForm->createView(),
            'error' => $error
        ]);
    }
}
