# wp-languages.github.io

## Features
- Supports **https** by using github.io ssl. Composer default configuration only accepts **https** by default starting from 2016.
- Automatically creates composer packages for wordpress translations from api.wordpress.org.
- This repo provides custom satis repository for wordpress languages. See more in [wp-languages.github.io](https://wp-languages.github.io/).
- Repos are **updated every 30-minutes**.
- If you would like to add more language files please submit a pull request.

## Use packages with composer
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://wp-languages.github.io"
        }
    ],
    "require": {
      "koodimonni-language/fi": "*",
      "koodimonni-language/fr_FR": "*"
    },
    "extra": {
      "dropin-paths": {
        "htdocs/wp-content/languages/": ["vendor:koodimonni-language"],
        "htdocs/wp-content/languages/plugins/": ["vendor:koodimonni-plugin-language"],
        "htdocs/wp-content/languages/themes/": ["vendor:koodimonni-theme-language"]
      }
    }
}
```

##Request packages
If package what you're looking for can't be found look first the response from api:

[https://api.wordpress.org/translations/core/1.0/](https://api.wordpress.org/translations/core/1.0/)

If your language is not listed in the response we can't help you. You'll need to ask from wp-core translators instead.

This is pretty static repository and we can't add all possible plugins here.

## How to use:

```
# Clone the project to your own server
$ git clone https://github.com/wp-languages/wp-languages.github.io /to/your/htdocs
$ cd /to/your/htdocs && composer install

# Set new cronjob
$ crontab -e
# Add following line to your cron
*/30 * * * * cd /to/your/htdocs && bash update.sh
```

## License
MIT License