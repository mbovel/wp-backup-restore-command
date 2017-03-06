Feature: Backup WordPress core

  Scenario: Backup the core of an installation in current working directory
    Given a WP install at version 4.7.1

    When I run `wp backup core`

    Then STDOUT should be:
    """
    {
        "version": "4.7.1",
        "locales": [],
        "path": ""
    }
    """

  Scenario: Backup the core of an installation in a subdirectoy
    Given a WP install at version 4.7.1 in 'wp'

    When I run `wp backup core --path=wp`

    Then STDOUT should be:
    """
    {
        "version": "4.7.1",
        "locales": [],
        "path": "wp"
    }
    """
