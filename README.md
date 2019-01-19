# Candela Utility

A plugin that extends Pressbooks for textbook authoring.

## Synopsis

[Pressbooks](https://github.com/pressbooks/pressbooks) is a plugin that turns your Wordpress multisite installation into a book publishing platform.
Candela Utility is a plugin that extends the functionality of Pressbooks by utilizing various action and filter hooks supplied by the Pressbooks API.

## Installation

### Composer

1.  From the root wordpress installation, add the following to `composer.json` (replacing `v0.4.0` with desired version):

    ```
    {
      "repositories": [
        {
          "type": "vcs",
            "url": "https://github.com/lumenlearning/candela-utility"
        }
      ],
      "require": {
        "lumenlearning/candela-utility": "v0.4.0"
      }
    }
    ```

1.  Run `composer install` in the terminal

### Manually

1.  Download or clone Candela Utility into your wordpress multisite plugins directory: `/path/to/wordpress/wp-content/plugins`
1.  Log in to your Wordpress multisite instance and navigate to `Network Admin > Plugins` and activate the Candela Utility plugin

*Note: Wordpress Multisite and Pressbooks are required in order for Candela Utility to work correctly*
