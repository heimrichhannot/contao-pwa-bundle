/**
 * Konfiguration für die Push-Benachrichtigungs-Übersetzungen.
 * @typedef {object} PwaPushNotificationTranslations
 * @property {string} subscribe - Text für den "Abonnieren"-Button/Aktion.
 * @property {string} unsubscribe - Text für den "Abbestellen"-Button/Aktion.
 * @property {string} blocked - Meldung, wenn Benachrichtigungen durch den Browser blockiert sind.
 * @property {string} not_supported - Meldung, wenn Benachrichtigungen vom Browser nicht unterstützt werden.
 */

/**
 * Enthält alle Übersetzungstexte für die PWA.
 * @typedef {object} PwaTranslations
 * @property {PwaPushNotificationTranslations} pushnotifications - Übersetzungstexte für Push-Benachrichtigungen.
 */

/**
 * Konfiguration für den Service Worker.
 * @typedef {object} PwaServiceWorkerConfig
 * @property {string} path - Der Pfad zum Service Worker Skript.
 * @property {string} scope - Der Geltungsbereich (Scope) des Service Workers.
 */

/**
 * Konfiguration für Push-Benachrichtigungen.
 * @typedef {object} PwaPushNotificationsConfig
 * @property {boolean} support - Gibt an, ob Push-Benachrichtigungen generell unterstützt werden sollen.
 * @property {string} subscribePath - Der API-Endpunkt zum Abonnieren von Benachrichtigungen.
 * @property {string} unsubscribePath - Der API-Endpunkt zum Abbestellen von Benachrichtigungen.
 */

/**
 * Hauptkonfigurationsobjekt für die PWA-Funktionalität.
 * @typedef {object} PwaConfig
 * @property {boolean} debug - Aktiviert den Debug-Modus.
 * @property {PwaServiceWorkerConfig} serviceWorker - Konfiguration für den Service Worker.
 * @property {PwaPushNotificationsConfig} pushNotifications - Konfiguration für Push-Benachrichtigungen.
 * @property {PwaTranslations} translations - Alle Übersetzungstexte.
 * @property {boolean} hideInstallPrompt - Verhindert, dass der Standard-Installations-Dialog angezeigt wird.
 */