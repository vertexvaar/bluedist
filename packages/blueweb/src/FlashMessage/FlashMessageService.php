<?php

namespace VerteXVaaR\BlueWeb\FlashMessage;

use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
use VerteXVaaR\BlueWeb\Enum\Severity;

readonly class FlashMessageService
{
    public function __construct(
        protected FlashMessageStore $flashMessageStore,
    ) {}

    public function add(Session $session, string $title, string $message, Severity $severity = Severity::INFO): void
    {
        $flashMessage = new FlashMessage($title, $message, $severity);
        $this->flashMessageStore->store($session->identifier, $flashMessage);
    }

    /**
     * @return array<FlashMessage>
     */
    public function get(Session $session): array
    {
        return $this->flashMessageStore->get($session->identifier);
    }
}
