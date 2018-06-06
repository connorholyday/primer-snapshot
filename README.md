# Primer Snapshot
A module for [Primer](https://github.com/Rareloop/primer) that provides simple visual regression testing using [Jest](https://facebook.github.io/jest/) and [Puppeteer](https://github.com/GoogleChrome/puppeteer).

*This module assumes you're using a Unix based system (e.g. Linux, Mac OS X). It hasn't been tested on Windows but almost certainly won't work without some tweaking!*

## Installation
This module isn't currently on Packagist so you'll need to add a custom repository to your `composer.json`.

````json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:connorholyday/primer-snapshot.git"
    }
]
````

Add the following to you `require` object:

````json
"rareloop/primer-snapshot": "dev-master"
````

We also need to install some non PHP dependencies so add the following to your `composer.json`:

````json
"scripts": {
    "post-install-cmd": [
        "cd vendor/rareloop/primer-snapshot && npm install"
    ],

    "post-update-cmd": [
        "cd vendor/rareloop/primer-snapshot && npm install"
    ]
}
````

Update your dependencies:

````
composer update
````

## Setup
Once installed you'll need to add some commands to Primer. Edit your `bootstrap/start.php` and add the following:

````php
Event::listen('cli.init', function ($cli) {
    $cli->add(new \Rareloop\Primer\Snapshot\Commands\Snapshot);
});
````

This will add a new command to the Primer CLI.

## Usage
````php
php primer snapshot [--options]
````

Run snapshots on all components

````php
php primer snapshot --update
````

Update all of the reference snapshots

## Options

### `--port[=8080]`
`default: 8080`
Set the port Primer is using

### `--elements`
Include all element patterns

### `--components[=false]`
`default: true`
Include all component patterns, you can pass false to disable this

### `--templates`
Include all template patterns

### `--url`
Full ID of a single pattern (e.g. components/group/name)

### `--update`
This updates all of the reference snapshots, it can be paired with any option to generate specific snapshots.
