<?php

namespace Contact\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackDTO;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackForm;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
class ContactController extends AbstractController
{
    #[Route('/contacts', name: 'index')]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        SupportFeedbackHandler $handler,
    ): Response
    {




        /** Форма обратной связи */
        $feedback = new SupportFeedbackDTO();
        $feedbackForm = $this
            ->createForm(
                SupportFeedbackForm::class,
                $feedback,
                [
                    'action' => $this->generateUrl('support:public.feedback'),
//                    'attr' => ['class' => 'bg-light d-flex row p-4 rounded-4 form-shadow feedback-form']
                ]
            )
            ->handleRequest($request);

        if($feedbackForm->isSubmitted() && $feedbackForm->isValid())
        {
            $this->refreshTokenForm($feedbackForm);

            /** SupportInvariableDTO */

            $SupportInvariableDTO = new SupportInvariableDTO();
            $SupportInvariableDTO
                ->setProfile($this->getProfileUid())
                ->setType(new TypeProfileUid(TypeProfileUser::TYPE))
                ->setTicket(uniqid('', true))
                ->setTitle($translator->trans('add.title', domain: 'support.public'));

            /** SupportDTO */

            $SupportDTO = new SupportDTO();
            $SupportDTO
                ->setStatus(new SupportStatus(SupportStatusOpen::PARAM))
                ->setPriority(new SupportPriority(SupportPriorityLow::PARAM))
                ->setInvariable($SupportInvariableDTO);

            /**
             * Сообщение с данными
             */
            $SupportMessageDataDTO = new SupportMessageDTO();
            $SupportMessageDataDTO
                ->setName($feedback->getName())
                ->setMessage($feedback->getMessage());

            /**
             * Сообщение с номером телефона
             */
            $SupportMessagePhoneDTO = new SupportMessageDTO();
            $SupportMessagePhoneDTO
                ->setName($feedback->getName())
                ->setMessage(
                    $translator->trans(
                        id: 'add.message',
                        parameters: ['phone' => $feedback->getPhone()],
                        domain: 'support.public'
                    )
                );

            $SupportDTO->addMessage($SupportMessageDataDTO);
            $SupportDTO->addMessage($SupportMessagePhoneDTO);

            $handle = $handler->handle($SupportDTO);

            $this->addFlash(
                'add.feedback',
                $handle instanceof Support ? 'add.success' : 'add.danger',
                'support.public',
                $handle
            );

            return $this->redirectToRoute('contact:index');
//            return $this->redirectToReferer();
        }

        return $this->render(['form' => $feedbackForm->createView(),]);




//        dd(__METHOD__);

//        return $this->render([
//            'controller_name' => 'ExampleController',
//        ]);
    }
}
