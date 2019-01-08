<?php

$lang = &$GLOBALS['TL_LANG']['tl_page'];

/**
 * Fields
 */
$lang['addPwa'] = ["Activate Progressive Web App (PWA)","Make current page an progressive web app."];
$lang['addPwa'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_NO] = 'No';
$lang['addPwa'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_YES] = 'Yes';
$lang['addPwa'][\HeimrichHannot\ContaoPwaBundle\DataContainer\PageContainer::ADD_PWA_INHERIT] = 'Inherit from parent page';

$lang['pwaConfiguration'] = ["PWA-Konfiguration auswählen","Wählen Sie hier die Konfiguration für die Progressive Web App."];
$lang['pwaParent'] = ["Eltern-Seite","Wählen Sie hier die Eltern-Seite für die Progressive Web App, von welcher der aktuelle Seitenbaum erben soll. Dies ist sinnvoll bei mehrsprachigen Seiten, welche die gleiche Konfiguration erhalten sollen."];

/**
 * Legends
 */

$lang['pwa_legend'] = 'Progressive Web App';