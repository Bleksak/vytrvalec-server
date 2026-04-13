Feature: User Login
  Scenario: Successful login

  Given there exists a user with email "existing@test.com"
  When I log in with email "existing@test.com" and password "VeryStrongPassword123@!"
  Then the logged in user should have email "existing@test.com", first name "Jiri", last name "Velek"

  Scenario: Login with invalid email

  Given there is no user registered with email "nonexistent@test.com"
  When I log in with email "nonexistent@test.com" and password "VeryStrongPassword123@!"
  Then I should receive an error that the user was not found

  Scenario: Login with wrong password

  Given there exists a user with email "existing@test.com"
  When I log in with email "existing@test.com" and password "WrongPassword123@!"
  Then I should receive an error that the password is invalid

  Scenario: Login with missing email

  Given there exists a user with email "existing@test.com"
  When I log in with email "" and password "VeryStrongPassword123@!"
  Then I should receive a validation error for the "email" field

  Scenario: Login with missing password

  Given there exists a user with email "existing@test.com"
  When I log in with email "existing@test.com" and password ""
  Then I should receive a validation error for the "password" field

  Scenario: Login with Firebase token

  Given there exists a user with email "existing@test.com"
  When I log in with email "existing@test.com", password "VeryStrongPassword123@!" and Firebase token "test-firebase-token"
  Then the logged in user should have email "existing@test.com", first name "Jiri", last name "Velek"
