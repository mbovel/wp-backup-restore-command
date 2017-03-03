Feature: Restore plugins

  Scenario: Restore a specific version of a plugin from wordpress.org which must be active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    wp plugin uninstall akismet
    """
    And a plugins.json file:
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

    When I run `wp restore plugins < plugins.json`

    Then STDOUT should contain:
    """
    Installing Akismet (3.1)
    """
    And STDOUT should contain:
    """
    Plugin 'akismet' activated.
    Success: Installed 1 of 1 plugins.
    """

  Scenario: Restore a specific version of a plugin from wordpress.org which must not be active
    Given a WP install
    And the following setup:
    """
    wp plugin uninstall hello
    wp plugin uninstall akismet
    """
    And a plugins.json file:
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

    When I run `wp restore plugins < plugins.json`

    Then STDOUT should contain:
    """
    Installing Akismet (3.1)
    """
    And STDOUT should not contain:
    """
    Plugin 'akismet' activated.
    """
    And STDOUT should contain:
    """
    Success: Installed 1 of 1 plugins.
    """
