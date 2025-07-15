# Contao Progressive Web App Bundle

A comprehensive bundle that provides Progressive Web App (PWA) support for Contao CMS, enabling modern web application features for your Contao websites.

## Features

* Create and register manifest files for each page root
* Generate and register service workers for each page root
* Send custom push notifications from the backend
* Set an offline fallback page for improved user experience
* Frontend module and content element to manage push notification subscriptions
* Includes a content element with an easy to use subscribe button
* [Encore Bundle](https://github.com/heimrichhannot/contao-encore-bundle) support, including asset precaching
* Expandable architecture for custom implementations

## Installation

### Install with Composer

```bash
composer require heimrichhannot/contao-pwa-bundle
```

After installation, update your database.

### Requirements

* PHP ^8.2
* Contao ^4.13 || ^5.0
* [Contao Head Bundle](https://github.com/heimrichhannot/contao-head-bundle)

### Additional Dependency for Push-Notifications

To enable web push notifications, you'll need to install [web-push-libs/web-push-php](https://github.com/web-push-libs/web-push-php) (versions 5 to 8 are supported):

```
composer require minishlink/web-push:^8.0
```

Additionally, ensure the following PHP extensions are installed and enabled:
* gmp
* mbstring
* curl
* openssl

## Setup

1. If you want to use push notifications, add VAPID keys to your configuration (see [VAPID Keys](#vapid-keys) section below).
2. Create a PWA Configuration in the Contao backend (System → PWA Configuration).
3. Add the configuration to a page root:
    * Navigate to page settings
    * Find the "Progressive Web App" section
    * Select "Yes" and choose your configuration
    * Upon saving, the page manifest and service worker will be generated automatically
4. To provide an option for users to register for push notifications, add either:
    * The Push Subscription Button content element, or
    * The push notification popup frontend module to your page

> Legacy (should no longer be required): Ensure your page template (typically `fe_page.html5`) supports [Head Bundle](https://github.com/heimrichhannot/contao-head-bundle). It must output at least `$this->meta()` in the head section. See [Head Bundle documentation](https://github.com/heimrichhannot/contao-head-bundle/blob/master/README.md) for more information.

### VAPID Keys

For push notifications, you need to add your server VAPID keys to your config file, typically in your project's `config.yml`. If you don't have VAPID keys yet, you can generate them in the PWA Bundle backend (Contao Backend → System → PWA Configuration → Control → Authentication).

```yaml
huh_pwa:
    vapid:
        subject: "mailto:your-email@example.org"
        publicKey: "YOUR_PUBLIC_KEY"
        privateKey: "YOUR_PRIVATE_KEY"
```

## Usage

### Regenerating Files

You can regenerate all manifest and service worker files at once from:
* The PWA Control panel (Contao Backend → System → PWA Configuration → Control → Files → Rebuild files)
* Or by using the command: `huh:pwa:build`

## Development

### JavaScript Events

To support custom controls, the bundle provides events and event listeners that can be used. All events are dispatched and listened to on the `document` object.

#### Events

| Event | Description |
|-------|-------------|
| huh_pwa_sw_not_supported | Fired if the browser doesn't support service workers or no service worker is found |
| huh_pwa_push_not_supported | Fired if the browser doesn't support push notifications |
| huh_pwa_push_permission_denied | Fired if push notifications are blocked in the browser |
| huh_pwa_push_isSubscribed | Fired when subscribed to push notifications (on page load or when subscribing) |
| huh_pwa_push_isUnsubscribed | Fired when unsubscribed from push notifications (on page load or when unsubscribing) |
| huh_pwa_push_subscription_failed | Fired when subscription to push notifications fails. Error reason can be found in event.detail.reason |
| huh_pwa_push_unsubscription_failed | Fired when unsubscribing from push notifications fails. Error reason can be found in event.detail.reason |

#### Listeners

| Event type | Usage | Description |
|------------|-------|-------------|
| huh_pwa_push_changeSubscriptionState | `new CustomEvent('huh_pwa_push_changeSubscriptionState', {detail: ['subscribe'|'unsubscribe']})` | Fire this event when the user interacts with your control to change their subscription state. Use a `CustomEvent` with detail parameter set to subscribe or unsubscribe |

### Complete Configuration

```yaml
huh_pwa:
    vapid:
        subject: "mailto:your-email@example.org"
        publicKey: "YOUR_PUBLIC_KEY"
        privateKey: "YOUR_PRIVATE_KEY"
    push:
        automatic_padding: 2847 # int (payload size in byte (min 0, max 4078)) or boolean (enable/disable)
    manifest_path: '/pwa' # where the manifest files should be located within web folder
    configfile_path: '/pwa' # where the configuration files should be located within web folder
```

### Commands

| Command           | Description |
|-------------------|-------------|
| huh:pwa:build     | (Re)Build config-specific files like service worker and manifest |
| huh:pwa:send-push | Send unsent push notifications |

### Adding Additional Head Scripts

You can add additional JavaScript code to the head section by using the `PwaHeadScriptTags` object available as `huh.head.tag.pwa.script` service. Code added with `addScript` will be output in the header section of your page within `<script type='text/javascript'>` tags.

### Polyfills

| Function | Example Polyfill | Description |
|----------|------------------|-------------|
| CustomEvent | [custom-event-polyfill](https://github.com/kumarharsh/custom-event-polyfill) | Custom events are needed to update the subscribe button (to inform the user that the browser doesn't support push notifications). An error is also thrown if the browser doesn't support CustomEvents |

## Todo

* Image size configuration
* Support for SVG icons
* Selective page precaching
* Customizable "Add to homescreen" and push notification prompts
