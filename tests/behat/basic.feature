@local @local_volunteer
Feature: Basic tests for volunteer

  @javascript
  Scenario: Plugin local_volunteer appears in the list of installed additional plugins
    Given I log in as "admin"
    When I navigate to "Plugins > Plugins overview" in site administration
    And I follow "Additional plugins"
    Then I should see "volunteer"
    And I should see "local_volunteer"
