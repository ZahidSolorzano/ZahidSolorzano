# Test Cases - Login Functionality
### TC-01: Valid login with standard user

| Field | Value |
|-------|-------|
| **Description** | Verify user can login with valid credentials |
| **Preconditions** | User is on login page (https://www.saucedemo.com) |
| **Test Data** | Username: "standard_user",  Password: "secret_sauce" |
| **Steps** | 1. Enter username "standard_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | User is redirected to products page  |
| **Actual Result** | User is redirected to products page |
| **Status** | PASS |
|**Priority** | HIGH |
---
### TC-02: Valid login with problem user

| Field | Value |
|-------|-------|
| **Description** | Verify problem_user can login successfully |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "problem_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username "problem_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | User is redirected to products page |
| **Actual Result** | User is redirected to products page |
| **Status** | PASS |
|**Priority** | HIGH |

---

### TC-03: Valid login with performance glitch user

| Field | Value |
|-------|-------|
| **Description** | Verify performance_glitch_user can login (may have delay) |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "performance_glitch_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username "performance_glitch_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | User is redirected to products page |
| **Actual Result** | User is redirected to products page after a 5 second delay |
| **Status** | PASS|
|**Priority** | HIGH |

---

### TC-04: Login with locked out user (negative test)

| Field | Value |
|-------|-------|
| **Description** | Verify locked_out_user cannot login and sees error message |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "locked_out_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username "locked_out_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | Error message should appear |
| **Actual Result** | Error message appears: "Epic sadface: Sorry, this user has been locked out." |
| **Status** |  PASS |
|**Priority** | HIGH |
---

### TC-05: Login with empty username

| Field | Value |
|-------|-------|
| **Description** | Verify error appears when username is empty |
| **Preconditions** | User is on login page |
| **Test Data** | Username: (empty), Password: "secret_sauce" |
| **Steps** | 1. Leave username field empty <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | Error message should appear|
| **Actual Result** | Error message: : "Epic sadface: Username is required" |
| **Status** | PASS |
|**Priority** | HIGH |

---

### TC-06: Login with empty password

| Field | Value |
|-------|-------|
| **Description** | Verify error appears when password is empty |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "standard_user", Password: (empty) |
| **Steps** | 1. Enter username "standard_user" <br> 2. Leave password field empty <br> 3. Click "Login" button |
| **Expected Result** | Error message should appear |
| **Actual Result** | Error message: "Epic sadface: Password is required"  |
| **Status** |  PASS |
|**Priority** | HIGH |
---

### TC-07: Login with invalid username

| Field | Value |
|-------|-------|
| **Description** | Verify error appears with invalid username |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "fakeuser123", Password: "secret_sauce" |
| **Steps** | 1. Enter invalid username "fakeuser123" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** |  Error message should appear |
| **Actual Result** |  Error message: "Epic sadface: Username and password do not match any user in this service"|
| **Status** | PASS |
|**Priority** | HIGH |
---

### TC-08: Login with invalid password

| Field | Value |
|-------|-------|
| **Description** | Verify error appears with invalid password |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "standard_user", Password: "wrongpassword" |
| **Steps** | 1. Enter username "standard_user" <br> 2. Enter invalid password "wrongpassword" <br> 3. Click "Login" button |
| **Expected Result** | Error message about invalid credentials |
| **Actual Result** | Error message appears as expected |
| **Status** | PASS |
|**Priority** | HIGH |
---

### TC-09 Login with all empty fields

| Field | Value |
|-------|-------|
| **Description** | Verify error appears with empty password and username|
| **Preconditions** | User is on login page |
| **Steps** | 1. Do not enter any data in any field  <br> 2. Click "Login" button |
| **Expected Result** | Error message should indicate that both fields are required  |
| **Actual Result** | Error message only says: "Username is required"|
| **Status** | PASS |
|**Priority** | HIGH |
---

