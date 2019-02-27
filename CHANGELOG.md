# Changelog
All notable changes to this project will be documented in this file.

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
