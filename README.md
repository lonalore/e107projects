e107projects (e107 v2 plugin)
=============================

This plugin is made for [e107projects.com](https://e107projects.com) website.

### Requirements

- e107 CMS >= v2.1.3 ([link](https://github.com/e107inc/e107))
- Composer Manager ([link](https://getcomposer.org))
- API Key for Google Places API Web Service ([link](https://console.developers.google.com))
- Secret Key for Github Webhooks ([link](https://developer.github.com/guides/basics-of-authentication))
- (plugin) **Social** (core)
- (plugin) **NodeJS** ([link](https://github.com/lonalore/nodejs))
- (plugin) **NodeJS Notify** ([link](https://github.com/lonalore/nodejs_notify))
- (library) **OpenLayers 3** ([link](https://openlayers.org))
- (library) **GeoComplete** ([link](https://github.com/ubilabs/geocomplete))
- (library) **OctIcons** ([link](https://octicons.github.com))
- (library) **nanoScroller.js** ([link](https://jamesflorentino.github.io/nanoScrollerJS))
- (library) **isotope** ([link](https://github.com/metafizzy/isotope))

### How to install?

- Install **NodeJS** and **NodeJS Notify** (well configured) plugins
- Install **Social** plugin, and enable Github login. Set you Client ID and Client Secret with scope "user:email public_repo write:repo_hook"
- Download **GeoComplete** library and put its files to `e107_web/lib/gecocomplete` folder
- Download **OpenLayers 3** library and put its files to `e107_web/lib/openlayers` folder
- Download **OctIcons** and put its files to `e107_web/lib/octicons` folder
- Download **nanoScroller.js** library and put its files to `e107_web/lib/jquery.nanoscroller` folder
- Download **isotope** library and put its files to `e107_web/lib/isotope` folder
- Upload **e107projects** plugin to `e107_plugins` folder
- Goto `e107_plugins/e107projects` folder, and run `composer install` from command line
- Install **e107projects** plugin
- Goto the plugin's settings page, and set your Google API key and Github Secret Key
