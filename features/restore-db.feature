Feature: Restore database

  Scenario: Restore the database of a simple installation of the latest WordPress version
    Given a WP install
    And the file simple_wp.sql

    When I run `wp restore db --url=http://example.com/wp < simple_wp.sql`
    And I run `wp option list --fields=option_name,option_value`

    Then STDOUT should be a table containing rows:
      | option_name | option_value          |
      | siteurl     | http://example.com/wp |
      | home        | http://example.com/wp |
      | blogname    | Backup DB Test        |
