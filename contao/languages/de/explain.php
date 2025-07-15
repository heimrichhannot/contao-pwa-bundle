<?php

$lang = &$GLOBALS['TL_LANG']['XPL'];

$lang['pwaPopupToggle'] = [
    [
        \HeimrichHannot\PwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule::TOGGLE_EVENT,
        'Das Popup wird bei öffnen der Website angezeigt, wenn der Browser Push-Subscriptions unterstützt und der Benutzer diese noch nicht abonniert hat.'
    ],
    [
        \HeimrichHannot\PwaBundle\FrontendModule\PushSubscriptionPopupFrontendModule::TOGGLE_CUSTOM,
        'Das Popup soll durch eine eigene Lösung geöffnet werden.'
    ]
];