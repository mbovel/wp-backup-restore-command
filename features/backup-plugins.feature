Feature: Backup plugins

  Scenario: Backup a specific version of a plugin from wordpress.org which is active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    wp plugin install akismet --version=3.1 --force --activate
    """

    When I run `wp backup plugins`

    Then STDOUT should be:
    """
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
    """

  Scenario: Backup a specific version of a plugin from wordpress.org which is not active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    wp plugin install akismet --version=3.1 --force
    """

    When I run `wp backup plugins`

    Then STDOUT should be:
    """
    {
        "akismet\/akismet.php": {
            "name": "Akismet",
            "path": "akismet\/akismet.php",
            "version": "3.1",
            "active": false,
            "source": "wordpress.org",
            "handle": "akismet"
        }
    }
    """

  Scenario: Backup the latest version of a plugin from wordpress.org which is not active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    """

    When I run `wp backup plugins`

    Then STDOUT should contain:
    """
    {
        "akismet\/akismet.php": {
            "name": "Akismet",
            "path": "akismet\/akismet.php",
    """
    And STDOUT should contain:
    """
            "active": false,
            "source": "wordpress.org",
            "handle": "akismet"
        }
    }
    """

  Scenario: Backup a specific version of a plugin from a foreign source which is not not active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    wp plugin uninstall akismet
    wp plugin install https://github.com/afragen/github-updater/archive/6.2.2.zip
    """

    When I run `wp backup plugins`

    Then STDOUT should be:
    """
    {
        "github-updater\/github-updater.php": {
            "name": "GitHub Updater",
            "path": "github-updater\/github-updater.php",
            "version": "6.2.2",
            "active": false,
            "source": null,
            "handle": null
        }
    }
    """
    And STDERR should be:
    """
    Warning: `wp restore` will not be able to automatically restore plugin GitHub Updater.
    """
