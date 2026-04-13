Feature: User Registration
  Scenario: Successful registration

  Given there is no user registered with email "jiri.velek7@protonmail.com"
  And there exists a faculty with ID 1
  When I send the registration form with email "jiri.velek7@protonmail.com", password: "VeryStrongPassword123@!", first name: "Jiri", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
  Then User with the email "jiri.velek7@protonmail.com" should exist in the database, with the first name: "Jiri", last name: "Velek", faculty id: "1"

  Scenario: Registration with invalid email

    Given there is no user registered with email "invalid-email"
    When I send the registration form with email "invalid-email", password: "VeryStrongPassword123@!", first name: "Jiri", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
    Then I should receive a validation error for the "email" field

  Scenario: Registration with weak password

    Given there is no user registered with email "weak.password@test.com"
    When I send the registration form with email "weak.password@test.com", password: "123", first name: "Jiri", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
    Then I should receive a validation error for the "password" field

  Scenario: Registration with missing required fields

    Given there is no user registered with email "missing.fields@test.com"
    When I send the registration form with email "missing.fields@test.com", password: "VeryStrongPassword123@!", first name: "", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
    Then I should receive a validation error for the "firstName" field

  Scenario: Registration with duplicate email

    Given there exists a user with email "existing@test.com"
    When I send the registration form with email "existing@test.com", password: "VeryStrongPassword123@!", first name: "Jiri", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
    Then I should receive an error that the email is already taken

  Scenario: Registration with non-existent faculty

    Given there is no user registered with email "wrong.faculty@test.com"
    And there is no faculty with ID 999
    When I send the registration form with email "wrong.faculty@test.com", password: "VeryStrongPassword123@!", first name: "Jiri", last name: "Velek", facultyId: "999", anonymize: "false", gdpr: "true"
    Then I should receive an error that the faculty does not exist
