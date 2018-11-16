# Contao Progressive Web App Bundle

A bundle to provide progressive web app support for contao.

> This bundle is still in early beta. Use at your own risk! Serviceworker precaching currently only works for Webpack/Encore enabled pages. 

This bundle is using [PHP Web Push lib](https://github.com/web-push-libs/web-push-php) to provide push notifications. 

## Features

* Create and register manifest file for each page root
* Create and register service worker for each page root
* Subscribe to Push Notifications button as Content Element
* send custom push notifications from backend

## Setup

### Requirements

* PHP >= 7.1
* PHP extensions:
    * gmp
    * mbstring
    * curl
    * openssl
* Contao >=4.4 

### Install

Call `composer require heimrichhannot/contao-pwa-bundle` and update database afterwards.

### First Steps

1. If you want to use push notifications, add vapid keys to your config (see [Setup -> Vapidkeys](#push-notifications))
2. Create an PWA Configuration(Backend -> System -> PWA Configuration)
3. Add the configuration to a page root (in page settings you find a new section "Progressive Web App", select yes and choose your configuration)
    * On saving the page the page manifest and the serviceworker will be generated
4. To provide an option to register to your push notifications, you need to add the Push Notification Subscribe Button content element on your page
 
### Push Notifications

#### VAPID keys

You need to add your server vapid keys to your config file, typical in your project config.yml. If you haven't already vapid keys, you can generate in the PWA Bundle backend (Contao Backend -> System -> PWA Configuration -> Control -> Authentication)

```yaml
huh_pwa:
  vapid:
    subject: "mailto:test@example.org"
    publicKey: "BPZACSEB_Efa3_e2XdVRm4M3Suga2WnhNs9THpVixfScWicSiA3ZYQ3zCG4Uez3EnbL3q-O2RomlZtYejva642M"
    privateKey: "W0qtmwq0aB47Swmid0uDZyW945p9b5bgv_WmfsmsRHw"
```


## Todo
* image size config
* support svg icons