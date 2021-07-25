# Description

This module integrates your Drupal 8 website with Unomi

It connects your site to the API of Unomi. It allows a pluggable
 authentication strategy to support local Unomi's or hosted Unomi services like
 Dropsolid Platform.

# Installation

* Install the Drupal 8 module via Composer:
  `composer require drupal/unomi --update-with-dependencies`
* Go to /admin/config/services/unomi and follow instructions to enter
  your credentials

# Usage

To interact with the Unomi API in your custom module, use the
`unomi_api` service, and call the `getClient()` method on it:

```
/** @var \Drupal\unomi\UnomiApiInterface $unomi_service */
$unomiService = \Drupal::service('unomi_api');
/** @var \Dropsolid\UnomiSdkPhp\Unomi $unomiClient */
$unomiClient = $unomi_service->getClient();
```

Please note that you should preferably use dependency injection to load the
service, instead of `\Drupal::service()`.

Now that you have access to the unomi client, you can interact with the
Unomi data objects, e.g. get a list of segments:

```
/**
 * @var \Dropsolid\UnomiSdkPhp\Repository\SegmentRepository $segmentRepository
 */
$segments_repository = $unomi_client->segments();
$segments = $segments_repository->listContacts();
```

# Check coding standards

Check Drupal coding standards

```
phpcs --standard=Drupal --extensions=php,module,inc,install,test,profile,\
theme,css,info,txt,yml .
```

Check Drupal best practices
```
phpcs --standard=DrupalPractice --extensions=php,module,inc,install,test,\
profile,theme,css,info,txt,md,yml .
```

Automatically fix coding standards
```
phpcbf --standard=Drupal --extensions=php,module,inc,install,test,profile,\
theme,css,info,txt,md,yml .
```
