<?php


namespace App\Listener;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddOwnerToTaskSubscriber implements EventSubscriberInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * AddOwnerToTaskSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['attachOwner', EventPriorities::PRE_WRITE],
        ];
    }

    public function attachOwner(ViewEvent $event) : void
    {
        $task = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$task instanceof Task || Request::METHOD_POST !== $method) {
            return;
        }

        // maybe these extra null checks are not even needed
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $owner = $token->getUser();
        if (!$owner instanceof User) {
            return;
        }


        // Attach the user to the not yet persisted Article
        $task->setUser($owner);

    }
}