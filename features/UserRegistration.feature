Feature: User Registration
  Scenario: Successful registration

  Given there is no user registered with email "jiri.velek7@protonmail.com"
  And there exists a faculty with ID 1
  When I send the registration form with email "jiri.velek7@protonmail.com", password: "VeryStrongPassword123@!", first name: "Jiri", last name: "Velek", facultyId: "1", anonymize: "false", gdpr: "true"
  Then User with the email "jiri.velek7@protonmail.com" should exist in the database, with the first name: "Jiri", last name: "Velek", faculty id: "1"
