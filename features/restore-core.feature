Feature: Restore WordPress core

  Scenario: Restore the core of an installation in current working directory
    Given an empty directory
    And a core.json file:
    """
    {
        "version": "4.7.1",
        "locales": [],
        "path": ""
    }
    """

    When I run `wp restore core < core.json`

    Then STDOUT should contain:
    """
    Downloading WordPress 4.7.1 (en_US)...
    """
    And STDOUT should contain:
    """
    Success: WordPress downloaded.
    """

    When I run `wp core version`

    Then STDOUT should be:
    """
    4.7.1
    """

  Scenario: Restore the core of an installation in a subdirectoy
    Given an empty directory
    And a core.json file:
    """
    {
        "version": "4.7.1",
        "locales": [],
        "path": "wp"
    }
    """

    When I run `wp restore core < core.json`

    Then STDOUT should contain:
    """
    Downloading WordPress 4.7.1 (en_US)...
    """
    And STDOUT should contain:
    """
    Creating directory
    """
    And STDOUT should contain:
    """
    Success: WordPress downloaded.
    """

    When I run `wp core version --path=wp`

    Then STDOUT should be:
    """
    4.7.1
    """
