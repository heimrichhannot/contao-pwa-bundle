# Contao Progressive Web App Bundle

A bundle to provide progressive web app support for contao.

> This bundle is currently in progress an not ready for production.

This bundle is using [PHP Web Push lib](https://github.com/web-push-libs/web-push-php) to provide push notifications. 

## Features

* Create and register manifest file for each page root
* Create and register service worker for each page root (Default service worker currently only works for page with build/manifest.json file in web root (precaches webpack assets)) and page scope `/de/`

## Setup

### Prerequisites

* PHP >= 7.1
* PHP GNU Multiple Precision extension needed due some dependencies
* Contao >=4.4 

### Push Notifications

#### VAPID keys

You need to add your server vapid keys to your config file, typical in your project config.yml.

```yaml
huh_pwa:
  vapid:
    subject: "mailto:test@example.org"
    publicKey: "BPZACSEB_Efa3_e2XdVRm4M3Suga2WnhNs9THpVixfScWicSiA3ZYQ3zCG4Uez3EnbL3q-O2RomlZtYejva642M"
    privateKey: "W0qtmwq0aB47Swmid0uDZyW945p9b5bgv_WmfsmsRHw"
```


## Todo
* translations 
* make service worker configurable
* image size config
* support svg icons