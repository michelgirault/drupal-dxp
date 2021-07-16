# Rocketship Theme Generator

This module contains a PHP-script to generate component based themes for use with [the Rocketship distribution](https://www.drupal.org/project/dropsolid_rocketship_profile) and its various components), based on certain presets.

The generated themes make use of Gulp as a workflow tool and Sass for preprocessing the CSS, as well as Storybook for styleguide generation. All info about how to use it, is present in the README file of your generated themes and the built Storybook styleguide.

This module replaces the 4 Rocketship themes previously maintained here, which should be considered legacy:
- https://www.drupal.org/project/rocketship_theme_demo
- https://www.drupal.org/project/rocketship_theme_flex
- https://www.drupal.org/project/rocketship_theme_starter
- https://www.drupal.org/project/rocketship_theme_minimal

Some things the resulting themes do:

- Templates, CSS and JS are component-based
- Provides font loading options
- Uses Sass (with globbing) to make CSS
- Can provide Sourcemaps for CSS and JS
- Can generate favicons
- Can generate critical CSS files
- Can generates a custom icon font or icon sprite
- Has better responsive tables
- Can override colors for the Rocketship Paragraphs (or Content Blocks, depending on the version)  background-color palette
- Example styling for content types (eg. blog)
  - !important! The Libraries are NOT loaded by default.

## Requirements

### for using the module
- php 7.3+

### for using the generated themes
- A Drupal Rocketship distribution using Rocketship Core 5.x (for themes 2.x), 6.x (for themes 3.x) and up
- Node: 14.x
- NPM: 6.x
- a recent version of gulp-cli
- dependent Drupal modules:
  - responsive_image
  - components
  - unified_twig_ext
  - search

See theme's Readme file for more info on versions and updates

## Usage

Generating your theme can be done by running the php script in this module, with options:

`php scripts/generate-theme.php`

Use these options:
- **name**: The name of your theme. Don't use dashes or special characters.
- **machine-name**: the machine name
- **theme-path**: location of the generated theme relative to where the script is located (the `dist` folder inside the module is default)
- **preset**:
  - minimal: a setup with only some basic and structural CSS
  - starter: some more styling, including for the Rocketship elements as well
  - flex: more styling for the Rocketship elements, following the Rocketship Flex guidelines & presets
  - demo: styling and presets for a Demo site
- **author**: person developing the theme
- **description**: a short description that goes in the info, composer & README files

## Presets

The presets follow a cascading principle:
- **minimal**: inherits all its contents from the `templates/themes/BASE` folder
- **starter**: does the same + overwrites files with what is in the `STARTER` folder
- **flex**: does the same thing as `starter` + overwrites files with what is located in the `FLEX` folder
- **demo**: Inherits from all the previous + adds custom stuff for the demo site

So you can see that the themes that will be generated using these presets, will go from very little files & configuration to a very full set of files and configuration.

## Generate

You should pass your options in this format (don't forget the single quotes if using spaces):
`php scripts/generate-theme.php --name='My theme' --machine-name=my_theme`

Examples per theme preset:

```
php scripts/generate-theme.php --name='Rocketship Minimal' --machine-name='rocketship_theme_minimal' --preset=minimal --author='rembrandx' --description='Component-based Drupal theme for use with Dropsolid Rocketship install profile, modules and other components.' --theme-path='dist'
```

```
php scripts/generate-theme.php --name='Rocketship Starter' --machine-name='rocketship_theme_starter' --preset=starter --author='rembrandx' --description='Component-based Drupal theme for use with Dropsolid Rocketship install profile, modules and other components.' --theme-path='dist'
```

```
php scripts/generate-theme.php --name='Rocketship Flex' --machine-name='rocketship_theme_flex' --preset=flex --author='rembrandx' --description='Component-based Drupal theme for use with Dropsolid Rocketship install profile, modules and other components.' --theme-path='dist'
```

```
php scripts/generate-theme.php --name='Rocketship Demo' --machine-name='rocketship_theme_demo' --preset=demo --author='rembrandx' --description='Component-based Drupal theme for use with Dropsolid Rocketship install profile, modules and other components.' --theme-path='dist'
```

### Demo theme
A demo theme with extensive styling for all current Rocketship components, such as Rocketship's Layouts and Content Blocks. To be used as a demo of what Dropsolid Rocketship can do.
If you want the build a site, you should use one of the other theme presets.

### Flex theme
A theme for building Rocketship websites.<br />
Comes with some pre-defined styling and Sass/Twig files to get you started.<br />
The styling follows the standards and structures for Dropsolid's 'Flex' projects, in which for Rocketship components play a big part (eg. Rocketship Layouts, Content Blocks, … some styling related to Content Types).

Contains more styling that the Rocketship Minimal theme but less than the Rocketship Demo theme: Styled RS Content Blocks

### Starter
Comes with some basic styling and Sass/Twig files to get you started.<br />
Contains a bit more pre-defined styles that the Rocketship Minimal theme but less than the Rocketship Flex theme or Rocketship Demo theme: Structural CSS for Content Blocks and Content Types.

### Minimal
A theme for building Rocketship websites.
Comes with some very basic styling and Sass/Twig files to get you started.
The most bare-bones of the Rocketship themes: structural CSS for the RS Content Blocks

