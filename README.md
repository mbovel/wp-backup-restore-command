# WP-CLI Backup/Restore command proposition

This is a prototype to test some ideas I emitted in [this discussion](https://github.com/wp-cli/ideas/issues/5). To fix english and code mistakes, please feel free to pull request here. For further ideas and comments, please participe directly to the original discussion: <https://github.com/wp-cli/ideas/issues/5>.

For an example of how this command could be used, see [example-wp-setup](https://github.com/mbovel/example-wp-setup).

## Backup/Restore core

The command `wp backup core > core.json` generates something like that:

	{
	    "version": "4.7.1",
	    "locales": [],
	    "path": ""
	}

where:

- `version` holds the WordPress version,
- `locales` holds a list of installed core translations (restoring those is not implemented yet),
- `path` holds the path to the WordPress installation (not sure if useful as the same can be set in a `wp-cli.yml` file).

`wp restore core < core.json` restores core files.

## Backup/Restore database

The command `wp backup db > db.sql` generates an SQL dump of the database, with installation URLs replaced by constants. For example, all instances of the home url are replaced by `HOME_URL`, instances of the uploads directory url by `UPLOADS_URL` and so on:

	...
	INSERT INTO `wp_options` VALUES
    (1,'siteurl','SITE_URL','yes'),
    (2,'home','HOME_URL','yes'),
    (3,'blogname','WP CLI Site','yes'),
	...

`wp restore db < db.sql` restores core files.

## Backup/Restore plugins

The command `wp backup plugins > plugins.json` generates something like that:

	{
	    "akismet\/akismet.php": {
	        "name": "Akismet",
	        "path": "akismet\/akismet.php",
	        "version": "3.1",
	        "active": true,
	        "source": "wordpress.org",
	        "handle": "akismet"
	    }
	}

Right now, the command can only backup and restore plugins from the official wordpress.org repository. The idea however would be to handle more sources: git and svn repositories, S3, etc. The exact content of the JSON object for each plugin would depend on its source.

## Implementation concerns

### Why JSON?

1. These files do not need to be edited by humans.
2. That increases potentiel ease of integration with other tools: no need for a YAML parser.

### Database dump replacements

It is useful to replace URLs in the backup because:

1. That makes it easy to restore the backup in a different setup where URLs are different.
2. That makes the dump more git-friendly: no differences if you backup from different environments.

These replacements are now implemented with naive text replacement on the SQL dump through `str_replace`. In real life, `wp search-replace` functionalities should be reused as much as possible for a safer and cleaner dump. It was not used in this prototype because it does not have an easy way to handle multiple replacements yet.

Please note the importance of having different constants for home, installation, uploads, plugins and content URLs, and the order in which the replacements are made. This is mandatory to support non-standard setups (see [example-wp-setup](https://github.com/mbovel/example-wp-setup) for example).

### Plugin name

The namespace WP-CLI uses to refer to plugins and themes seems to be inconsistent. For uninstalling and other commands, the file name (see `WP_CLI\Utils\get_plugin_name`) is used:

	wp plugin uninstall hello

Wherever for installing, the handle from wordpress.org is used:

	wp plugin install hello-dolly

As in this example, these two strings do not always match.

In this prototype, both the full path of the plugin's main file and the worpress.org handle are saved in the backup JSON file.

When restoring plugins, each one is reinstalled unless there is an already installed plugin that match all of the following characteristics: path, handle, source, version *and* wherever it should be activated or not (and a files checksum could be added).
