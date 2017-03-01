Feature: Test that WP-CLI loads.

  Scenario: One outdated active plugin
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

  Scenario: One outdated inactive plugin
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

  Scenario: One up-to-date active plugin
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
