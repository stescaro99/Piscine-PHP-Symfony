<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if ($locale = $request->query->get('_locale')) {
            $request->setLocale($locale);
            $session->set('_locale', $locale);
        } elseif ($session->has('_locale')) {
            $request->setLocale($session->get('_locale'));
        }
    }
}
