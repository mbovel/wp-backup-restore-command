Feature: Backup database

  Scenario: Backup the database of a simple installation of the latest WordPress version
    Given a WP install

    When I run `wp backup db`

    Then STDOUT should contain:
    """
    INSERT INTO `wp_options` VALUES
    (1,'siteurl','SITE_URL','yes'),
    (2,'home','HOME_URL','yes'),
    (3,'blogname','WP CLI Site','yes'),
    """
