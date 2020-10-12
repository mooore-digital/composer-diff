# Composer Diff

Get insight into the composer.lock changes between branches.

## Installation

Make sure your global composer directory is in your `$PATH`. It either is:
- `~/.config/composer/vendor/bin` or
- `~/.composer/vendor/bin`.

``` shell
composer global require mooore/composer-diff
```

## Usage

### Synopsis

```
composer-diff <start_revision> [<end_revision>]
```

Revisions can be anything you can pass to `git show`, (i.e. a tag, branch or commit hash.)

**start_revision**: The revision from which the diff starts.

**end_revision**: (OPTIONAL) The revision to differentiate to. If you omit this option, the `composer.lock` of your current working directory will be used.

### Example

``` shell
$ composer-diff origin/master
Deleted packages:
- zendframework/zend-captcha
- zendframework/zend-code
- zendframework/zend-config
- zendframework/zend-console
- zendframework/zend-crypt
Added packages:
+ laminas/laminas-captcha
+ laminas/laminas-code
+ laminas/laminas-config
+ laminas/laminas-console
+ laminas/laminas-crypt
Upgraded packages:
~ symfony/console (v4.1.12 => v4.4.15)
~ symfony/css-selector (v4.4.8 => v4.4.15)
~ symfony/dom-crawler (v3.4.40 => v3.4.45)
~ symfony/event-dispatcher (v4.3.11 => v4.4.15)
~ symfony/event-dispatcher-contracts (v1.1.7 => v1.1.9)
~ symfony/filesystem (v4.4.13 => v4.4.15)
```
