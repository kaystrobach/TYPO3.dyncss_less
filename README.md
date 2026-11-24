# TYPO3.dyncss_less

This extension is a plugin for [dyncss](https://github.com/kaystrobach/TYPO3.dyncss), it adds less compiling
capabilities: every `*.less` file added to the page renderer is compiled to CSS.

```typoscript
page.includeCSS.theme = EXT:my_theme/Resources/Private/Less/theme.less
```

Variables can be overridden with `plugin.tx_dyncss.overrides`, see the README of dyncss.

## Requirements

TYPO3 12.4, 13.4 or 14 and dyncss from the branch `typo3-12-13-14` (branch `typo3-12-13-14` of this extension).

## Compiler

The extension ships less.php 1.7 (`Resources/Private/Php/less.php`), which is only loaded if no other less.php is
installed. With [wikimedia/less.php](https://packagist.org/packages/wikimedia/less.php) installed via composer, that one
is used.

## Imports between extensions (composer mode)

Imports written for the classic layout below `typo3conf/ext/` are resolved in composer mode as well:

```less
@import "../../../../theme_bootstrap/Resources/Private/Dyncss/import.less";
```

The first folder after the `../` is taken as extension key (`KayStrobach\Dyncss\Utilities\LegacyExtensionPath` of
dyncss), the child themes of theme_bootstrap (highland, appstrap, mosaic, …) need this.

## Errors

A less error does not break the page: the generated stylesheet contains the error message as comment, e.g.

```css
/* variable @brand-color is undefined in file …/variables.less in variables.less on line 12, column 8 */
```

## Tests

Functional tests in docker against TYPO3 12, 13 and 14, with the dyncss clone next to this one (`../dyncss`, otherwise
the branch `typo3-12-13-14` from GitHub):

    Build/Scripts/runTests.sh -t 12                     # TYPO3 12, also 13 or 14, bundled less.php
    LESS_PHP=wikimedia Build/Scripts/runTests.sh -t 12  # with wikimedia/less.php
    Build/Scripts/runTests.sh -a                        # all versions

