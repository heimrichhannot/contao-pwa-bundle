# Contao Progressive Web App Bundle

A bundle to provide progressive web app support for contao.

> This bundle is currently in progress an not ready for production.

## Features

* Create and register manifest file for each page root
* Create and register service worker for each page root (Default service worker currently only works for page with build/manifest.json file in web root (precaches webpack assets)) and page scope `/de/`

## Todo
* translations 
* make service worker configurable
* add push notifications to service worker
* image size config
* support svg icons