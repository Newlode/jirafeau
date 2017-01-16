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

.
├── admin.php : adminitration interface, also permits to download files
├── f.php : permits to download files or show the download page
├── index.php : only provide a html/javascript client to interact with API
├── script.php : API interface and it's html documentation
├── install.php : installation script
├── tos.php : terms of use the user may edit
├── lib
│   ├── config.local.php : user's parameters
│   ├── config.original.php : default parameters with their documentation
│   ├── functions_*.js : javascript functions for html/javascript client
│   ├── functions.php : core functions and tools of jirafeau
│   ├── locales : langage folder, contain all langage files
│   └── template
│       ├── footer.php
│       └── header.php
├── media : folder containing all skins
└── var-xxxxxxx : folder containing all data
    ├── async : chunks of uploaded files
    ├── files : all files that has been successfully uploaded
    └── links : all links pointing to files with meta-informations

## Translations

Translation may be add via [Jirafeau's Weblate](https://hosted.weblate.org/projects/jirafeau/master/).

## Coding style

- PHP function keywords are alone on a line
- Braces "{" must be put in a new line
- Files must be in UTF-8 without BOM and use Unix Line Endings (LF)

The whole project is not clean about that yet, feel free to fix :)

## Merge Requests

Please create one branch for each feature and send one merge request for each branch. 

Dont squash several changes or commits into one merge request as this is hard to review.
