# wp-languages.github.io
[![License](http://img.shields.io/:license-mit-blue.svg)](http://doge.mit-license.org)

## Features
- Supports **https** by using github.io ssl. Composer default configuration only accepts **https** by default starting from 2016.
- Automatically creates composer packages for WordPress translations from api.wordpress.org.
- This repo provides custom satis repository for WordPress languages. See more in [wp-languages.github.io](https://wp-languages.github.io/).
- To add more language files please submit a pull request to the `src` branch.
- Repos are updated hourly with GitHub Actions.

## Example configuration with composer

This example adds all translations from finnish and french packages.
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://wp-languages.github.io",
            "only": [
                "koodimonni-language/*",
                "koodimonni-plugin-language/*",
                "koodimonni-theme-language/*"
            ]
        }
    ],
    "require": {
        "koodimonni-language/fi": "*",
        "koodimonni-language/fr_fr": "*"
    },
    "extra": {
        "dropin-paths": {
            "htdocs/wp-content/languages/": [
                "vendor:koodimonni-language"
            ],
            "htdocs/wp-content/languages/plugins/": [
                "vendor:koodimonni-plugin-language"
            ],
            "htdocs/wp-content/languages/themes/": [
                "vendor:koodimonni-theme-language"
            ]
        }
    }
}
```

## Request packages
If package what you're looking for can't be found look first the response from api:

[https://api.wordpress.org/translations/core/1.0/](https://api.wordpress.org/translations/core/1.0/)

If your language is not listed in the response we can't help you. You'll need to ask from wp-core translators instead.

This is pretty static repository, and we can't add all possible plugins here.

## Manually adding any language zip to your composer.json
There's also manual method of including any translation in WordPress.org. This is useful because we can't include all plugins in this repository. Let's look how to add french (fr_FR) jetpack translations. First search the api for jetpack translations:

```bash
$ curl -s 'https://api.wordpress.org/translations/plugins/1.0/?slug=jetpack' | python -m json.tool
```

Choose your language zip from json output for example for us it was:
https://downloads.wordpress.org/translation/plugin/jetpack/3.9.2/fr_FR.zip

Then add it to your composer like this example. You just need to update the version number everytime the package updates.
```json
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "koodimonni-plugin-language/jetpack-fr_fr",
                "type": "wordpress-language",
                "version": "3.9.2",
                "dist": {
                    "type": "zip",
                    "url": "https://downloads.wordpress.org/translation/plugin/jetpack/3.9.2/fr_FR.zip",
                    "reference": "master"
                }
            }
        }
    ],
    "require": {
        "koodimonni/composer-dropin-installer": "*",
        "koodimonni-plugin-language/jetpack-fr_fr": ">=3.9.2"
    },
    "extra": {
        "dropin-paths": {
            "htdocs/wp-content/languages/": [
                "vendor:koodimonni-language"
            ],
            "htdocs/wp-content/languages/plugins/": [
                "vendor:koodimonni-plugin-language"
            ],
            "htdocs/wp-content/languages/themes/": [
                "vendor:koodimonni-theme-language"
            ]
        }
    }
}
```

## How to use this project as self-hosted version:

```bash
# Clone the project to your own server
git clone https://github.com/wp-languages/wp-languages.github.io /to/your/htdocs
cd /to/your/htdocs
composer install

# Set new cronjob
crontab -e

# Add following line to your cron
0 */2 * * * cd /to/your/htdocs && composer run-script cache-build
```

And serve the generated `docs` folder.

## License
MIT License
