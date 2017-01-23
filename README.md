Dandelion v6.2.0
================

Dandelion is a web-based logbook designed to make it dead simple to keep logs. Dandelion helps you remember what you did four months ago. Dandelion developed out of the mindset of IT but is versatile enough to use in just about any situation.

Website and Docs: http://blog.onesimussystems.com/dandelion

Requirements
------------

* Apache or Nginx web server
    - mod_rewrite must be enabled for Apache
* PHP >= 7.0
* MySQL/Maria DB or SQLite PHP module

Dandelion has been tested on Ubuntu with Apache and Nginx. Other combos may probably work but YMMV.

Is it any good?
---------------

[Yes](https://news.ycombinator.com/item?id=3067434)

Installation Instructions
-------------------------

Installation docs are available on the website [here](http://blog.onesimussystems.com/dandelion/install/).

Chrome Extension for Cheesto
----------------------------

I've also taken the time to develop a small Chrome extension that can interface with any Dandelion installation version 5 and above. The extension is available for install on the [Chrome Store](https://chrome.google.com/webstore/detail/cheesto-user-status/npggfenlbmepblpeenickeifmiionmli) and is free and released under the GPL v3 like Dandelion. The source is available on [GitHub](https://github.com/dragonrider23/Cheesto-Chrome).

Versioning
----------

For transparency into the release cycle and in striving to maintain backward compatibility, Dandelion is maintained under the Semantic Versioning guidelines. Sometimes we screw up, but we'll adhere to these rules whenever possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

- Breaking backward compatibility **bumps the major** while resetting minor and patch
- New additions without breaking backward compatibility **bumps the minor** while resetting the patch
- Bug fixes and misc changes **bumps only the patch**

For more information on SemVer, please visit <http://semver.org/>.

License - GPL v3
----------------

Dandelion - Web-based logbook.
Copyright (C) 2015  Lee Keitel, Onesimus Systems

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
