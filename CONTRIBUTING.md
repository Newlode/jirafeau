# Contributing

Hi,

this document is made for newcomers in Jirafeau who are digging into the code.

## General principle

Jirafeau is made in the [KISS](http://en.wikipedia.org/wiki/KISS_principle) way (Keep It Simple, Stupid).

It is meant to be a simple filehosting service, simple to use, simple to install, simple to maintain.

This project won't evolve to a file manager and will focus to keep a very few dependencies.

So things like a markdown parser for the ToS or E-Mail tasks would be usefull for sure, but may be [rejected](https://gitlab.com/mojo42/Jirafeau/issues/37#note_1191566) since they would a lot of dependencies and makes the project more complex.

## Structure

Here is a little explaination of Jirafeau's arboresence in a simplified
view only to show the most importants files and their role.

```
.
├── admin.php : administration interface to manage links and files
├── f.php : permits to download files or show the download page
├── index.php : provides a web interface to interact with API
├── script.php : API interface (all file actions happen here - upload, deletion, etc)
├── install.php : installation script
├── tos.php : "Terms of Service" page
├── lib
│   ├── config.original.php : default parameters
│   ├── config.local.php : the users parameters (auto generated, not versionized)
│   ├── functions_*.js : JavaScript functions for index.php (AJAX etc)
│   ├── functions.php : core functions and tools of Jirafeau
│   ├── tos.original.txt : default text show on the ToS page
│   ├── tos.local.txt : a users alternative text show on the ToS page (not versionized)
│   ├── settings.php : core settings of Jirafeau, includes the configuration params automatically
│   ├── locales : language folder, contains all language files
│   └── template
│       ├── footer.php : footer with links to source and ToS for all HTML views
│       └── header.php : header with logo and title for all HTML views
├── media : folder containing all skins
└── var-xxxxxxx : the users folder containing all data (auto generated, not versionized)
    ├── async : chunks of uploaded files (not succressfull yet) 
    ├── files : all files that have been uploaded successfully
        ├── [hashed file name] : the original file
        ├── [hashed file name]_count : count many links to this file exist
    └── links : all links, including meta-informations, pointing to files
        ├── [link] : the link file, includes which original file should be used and some meta data like creation date, expiration time
```

## Translations

Translation may be add via [Jirafeau's Weblate](https://hosted.weblate.org/projects/jirafeau/master/).

## Coding style

- This project follows the [PSR-2](http://www.php-fig.org/psr/psr-2/) Coding Style
- Files must be in UTF-8 without BOM and use Unix Line Endings (LF)

## Branches

* ```master``` = latest release, e.g. 2.0.1
* ```next-release``` = development branch - all new features are merged into this branch until the next version is released. So use this branch as base while developing new features or bugfixes.
* ```test``` = sandbox branch to test new features or merge requests, or run integration tests. The content of this branch may change at any time.

## Merge Requests

Please create one branch for each feature and send one merge request for each branch. 

Dont squash several changes or commits into one merge request as this is hard to review.

Please use ```next-release``` as base branch and send your merge request to this branch (not ```master```).
