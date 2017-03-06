Feature: Local behat steps work as excpected

  Scenario: Given a WP install at a specific version
    Given a WP install at version 4.7.1

    When I run `wp core version`

    Then STDOUT should be:
    """
    4.7.1
    """
