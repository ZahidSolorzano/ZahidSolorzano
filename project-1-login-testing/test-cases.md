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
| **Description** | Verify performance_glitch_user can login |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "performance_glitch_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username "performance_glitch_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | User is redirected to products page |
| **Actual Result** | User is redirected to products page after a 5 second delay |
| **Status** | PASS|
|**Priority** | HIGH |
|**Note:** | The 5-second delay is the intended behavior for this test account, not a bug. |
---

### TC-04: Login with locked out user

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
| **Status** | FAIL |
|**Priority** | MEDIUM |
| **Bug Reference** | BUG-01 |
---

### TC-10: Password field masks characters

| Field | Value |
|-------|-------|
| **Description** | Verify that password characters are masked (shown as dots or asteriks)|
| **Preconditions** | User is on login page |
| **Test Data** |  Password: "secret_sauce" |
| **Steps** | 1. Enter password "secret_sauce" <br> 2. Observe Behavior|
| **Expected Result** |  Password characters are masked |
| **Actual Result** |  Password characters are masked (shown as dots) |
| **Status** | PASS |
|**Priority** | MEDIUM |
---

### TC-11: Login with error_user

| Field | Value |
|-------|-------|
| **Description** | Verify that error_user can log in but experiences failures in cart/checkout actions |
| **Preconditions** | User is on https://www.saucedemo.com |
| **Test Data** | Username: "error_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username: "error_user" <br> 2. Enter password: "secret_sauce" <br> 3. Click "Login" button|
| **Expected Result** | Login should be successful <br> Cart or checkout actions should FAIL |
| **Actual Result** | Login successful <br> User logins to the inventory page with 6 items already added to the cart that cannot be removed |
| **Status** | PASS |
|**Priority** | HIGH |
---

### TC-12: Login with visual_user

| Field | Value |
|-------|-------|
| **Description** | Verify that visual_user can log in but has UI rendering issues |
| **Preconditions** | User is on https://www.saucedemo.com |
| **Test Data** | Username: "visual_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username: "visual_user" <br> 2. Enter password: "secret_sauce" <br> 3. Click "Login" button <br> 4. Observe product images and layout <br> 5. Compare with standard_user view |
| **Expected Result** | Login should be successful <br> UI elements (images, buttons, alignment) may appear incorrect |
| **Actual Result** | [Fill after testing] |
| **Status** | ⏳ PENDING |

