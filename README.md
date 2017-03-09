# Auto Link plugin for Craft CMS

Links keywords in your content to other entries or urls

## Installation

To install Auto Link, follow these steps:

1. Download & unzip the file and place the `autolink` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/tijsvanerp/craft-autolink.git` directly into your `craft/plugins/autolink` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require /autolink`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `autolink` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Auto Link works on Craft 2.6.x.

## Auto Link Overview

Craft Auto Link automatically replaces certain keywords/phrases in your HTML with assigned links. This can be used to improve the SEO value of certain keywords.

## Configuring Auto Link
After installing Auto Link go to the Auto Link settings to set the HTML tags that should be parsed by the Auto Link plugin. By minimizing the amount of tags that are handled by the plugin you can greatly improve performance. The plugin has no opinion on which HTML tags are handled (only `<a>` tags are excluded as it may yield strange results), so keep in mind that using the plugin on tags that are not designed to hold `<a>` tags may not work as you expect. HTML nodes that don't hold any `text()` values are ignored by default.
## Using Auto Link

Autolink is implemented as a Twig filter and Twig function and can be applied on your content in the following way:
```twig
    {{ entry.body | autolink }}

    {{ autoLink(entry.body) }}
```
It is possible to append extra classes to the `<a>` tag by using:
```twig
    {{ entry.body | autoLink({class: "c-link--auto-link"}) }}

    {{ autoLink(entry.body, {class: "c-link--auto-link"}) }}
```

The function implementation can be used to have more predictable results when using multiple filters or dynamic variables

## Auto Link Roadmap

Some things to do, and ideas for potential features:
* Auto link sections
* Regular expresion matching and linking/replacement
* Drag and drop reordering

Brought to you by [Tijs van Erp](https://github.com/tijsvanerp)
