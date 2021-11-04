# Changelog
All notable changes to this project will be documented in this file.

## [0.8.6] - 2021-11-04
- Changed: show error message when an exception occurs when generating a manifest file while saving a page instead of throwing the exception

## [0.8.5] - 2021-05-14
- fixed missing dateAdded setup in tl_pwa_pushnotifications
- fixed missing icon on push notifications
- fixed copy notification not working correctly

## [0.8.4] - 2021-04-26
- added image support to push notification popup module default template

## [0.8.3] - 2021-04-26
- added image support to push notification popup module

## [0.8.2] - 2021-04-22
- removed trailing ! in push_subscription templates

## [0.8.1] - 2021-04-15
- removed a dev leftover

## [0.8.0] - 2021-04-15
- Add push notification popup module ([#13])
- updated encore bundle integration, minimum supported encore bundle version is now 1.5
- removed empty parameters.yml file
- fixed a missing locale

## [0.7.4] - 2020-09-23
- fixed error in config.php in contao 4.9 
- fixed missing block in backend template (#11)

## [0.7.3] - 2020-08-04
- fixed InvalidArgumentException in contao 4.9 when using notification icon ([#10], jelomada)

## [0.7.2] - 2020-07-17
- fixed wrong variable name

## [0.7.1] - 2020-07-16
- fixed send date not correct displayed for non published messages in backend
- fixed support for contao 4.9 (tl_page rootfallback palette) (#8)

## [0.7.0] - 2020-05-27
- added open url option to push notification click event
- added dca picker for news to open url on click field
- fixed error row in send command

## [0.6.2] - 2019-11-14
* added service definitions for symfony 4

## [0.6.1] - 2019-10-29

* fixed exception handling

## [0.6.0] - 2019-10-29

* made minishlink/web-push optional as not working in all hosting environments

## [0.5.0] - 2019-07-16

* [INTERNAL] change config parameter from huh.pwa to huh_pwa
* exchanged babel polyfill for corejs, used only in precompiled source
* updated some dependencies
* small enhancements
* updated translations
* added licence text

## [0.4.3] - 2019-07-16

#### Fixed
* removed package-lock.json file

## [0.4.2] - 2019-02-29

#### Fixed
* published and start fields not visible for non-admins
* updated translations

## [0.4.1] - 2019-02-28

#### Fixed
* error due changes in Head bundle

## [0.4.0] - 2019-02-28

#### Added
* better output for build command
* support for `pushsubscriptionchange` event (not tested)

### Changed 
* moved HuhPwaServiceWorker class from Serviceworker to own file
* moved a lot of functionality out of twig templates to the HuhPwaServiceWorker class
* some refactoring


#### Fixed
* undefined error on install event
* some async errors
* notifications not marked sent

## [0.3.2] - 2019-02-27

#### Fixed
* added support to `heimrichhannot/contao-amp-bundle`, do not invoke custom js and manifest if amp pages enabled

## [0.3.1] - 2019-02-22

#### Fixed
* offline function not working correctly

## [0.3.0] - 2019-02-19

#### Changed
* increased WebPush lib dependency to 5
* changed start field of push notifications to mysql varchar type
* PushNotificationSender now throws errors
* reordered PWA config dca operation buttons

#### Added
* option to configure push message encryption padding

#### Fixed
* push notifications not arrive in firefox mobile
* notification titles not shows


## [0.2.1] - 2019-02-19

#### Fixed
* an undefined error in PushSubscriptionBottons.js

#### Changed
* moved translations to HuhContaoPwaBundle js class
* removed translation.js.twig

## [0.2.0] - 2019-02-19

#### Changed 
* Added publish, start and dateSent fields to tl_pwa_pushnotifications, removed sendDate field
* enhanced translations
* renamed some methods

#### Fixed 
* send push notifiations not respect sendDate

## [0.1.5] - 2019-02-18

#### Changed
* increased utils bundle dependency
* POST requests not cached
* clone response (no duplicate request)

#### Fixed
* Formhybrid asynchronous forms not working

## [0.1.4] - 2019-02-18

#### Fixed
* "Table is not allowed error" (#2)

## [0.1.3] - 2019-02-04

#### Fixed
* `BuildPwaFilesCommand` error if no pages with active pwa configuration exist

## [0.1.2] - 2019-01-23

#### Changed
* removed `type='text/javascript'` from header `script` tags
* updated yarn dependencies

## [0.1.1] - 2019-01-22

#### Changed
* updated the non-encore javascript

## [0.1.0] - 2019-01-22

Initial release

[#13]: https://github.com/heimrichhannot/contao-pwa-bundle/pull/13
[#10]: https://github.com/heimrichhannot/contao-pwa-bundle/pull/10
