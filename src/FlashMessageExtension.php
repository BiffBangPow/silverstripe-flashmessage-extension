<?php

namespace BiffBangPow\Extension;

use Psr\Container\NotFoundExceptionInterface;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class FlashMessageExtension extends Extension
{
    const FLASH_MESSAGES = 'flashmessages';

    /**
     * @param string $message The message to display
     * @param string $type (Optional) A class (or classes) to apply to the message.
     * @throws NotFoundExceptionInterface
     */
    public function flashMessage($message, $type = 'success')
    {
        $request = Injector::inst()->get(HTTPRequest::class);

        /** @var Session $session */
        $session = $request->getSession();

        $messages = $this->getMessages();

        $messages[] = ['Message' => $message, 'Type' => $type];
        $session->set('flashmessages', json_encode($messages));
    }

    /**
     * @return array|mixed
     * @throws NotFoundExceptionInterface
     */
    public function getMessages()
    {
        $session = $this->getSession();
        return $session->get(self::FLASH_MESSAGES) ? json_decode($session->get(self::FLASH_MESSAGES)) : [];
    }

    /**
     * @return Session
     * @throws NotFoundExceptionInterface
     */
    public function getSession()
    {
        $request = Injector::inst()->get(HTTPRequest::class);
        $session = $request->getSession();

        return $session;
    }

    /**
     * @return ArrayList
     * @throws NotFoundExceptionInterface
     */
    public function FlashMessages()
    {
        $messages = $this->getMessages();

        $return = new ArrayList();
        foreach ($messages as $message) {
            $return->push(new ArrayData($message));
        }

        $session = $this->getSession();
        $session->clear(self::FLASH_MESSAGES);

        return $return;
    }

    /**
     * @return bool
     * @throws NotFoundExceptionInterface
     */
    public function FlashMessagesPresent()
    {
        return $this->getSession()->get(self::FLASH_MESSAGES) ? true : false;
    }
}
