# Test Cases - Login Functionality

### TC-01: Valid login with standard user

| Field | Value |
|-------|-------|
| **Title** | Verify user can login with valid credentials |
| **Preconditions** | User is on login page (https://www.saucedemo.com) |
| **Test Data** | Username: "standard_user", Password: "secret_sauce" |
| **Steps** |1. Enter username "standard_user" 2. Enter password "secret_sauce" 3. Click "Login" button |
|**Expected Result**| User is redirected to products page|
|**Actual Result**|User is redirected to products page  |
|**Status**|PASS|

---

### TC-02: Valid login with problem user

| Field | Value |
|-------|-------|
| **Description** | Verify problem_user can login successfully |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "problem_user", Password: "secret_sauce" |
|**Steps**|1. Enter username "problem_user" 2. Enter password "secret_sauce" 3. Click "Login" button|
|**Expected Result** |User is redirected to products page | 
|**Actual Result**|User is redirected to products page  
|**Status**| PASS|

---

### TC-03: Valid login with performance glitch user

| Field | Value |
|-------|-------|
| **Description** | Verify performance_glitch_user can login |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "performance_glitch_user", Password: "secret_sauce" |
|**Steps**| 1. Enter username !performance_glitch_user" 2. Enter password "secret_sauce" 3. Click "Login" button|
|**Expected Result**| User is redirected to products page|
|**Actual Result**| User is redirected to products page after a 5 second delay  |
|**Status**| PASS |

---

### TC-04: Login with locked out user

| Field | Value |
|-------|-------|
| **Description** | Verify locked_out_user cannot login and sees error message |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `locked_out_user`, Password: `secret_sauce` |
|**Steps:** |1. Enter username `locked_out_user`2. Enter password `secret_sauce` 3. Click "Login" button|
|**Expected Result:** |Error message appears: "Epic sadface: Sorry, this user has been locked out."  |
|**Actual Result:**| Error message appears as expected  |
|**Status:** |PASS|

---

### TC-05: Login with empty username

| Field | Value |
|-------|-------|
| **Description** | Verify error appears when username is empty |
| **Preconditions** | User is on login page |
| **Test Data** | Username: (empty), Password: `secret_sauce` |

**Steps:**
1. Leave username field empty
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** Error message: "Epic sadface: Username is required"  
**Actual Result:** Error message appears as expected  
**Status:** ✅ PASS

---

### TC-06: Login with empty password

| Field | Value |
|-------|-------|
| **Description** | Verify error appears when password is empty |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `standard_user`, Password: (empty) |

**Steps:**
1. Enter username `standard_user`
2. Leave password field empty
3. Click "Login" button

**Expected Result:** Error message: "Epic sadface: Password is required"  
**Actual Result:** Error message appears as expected  
**Status:** ✅ PASS

---

### TC-07: Login with invalid username

| Field | Value |
|-------|-------|
| **Description** | Verify error appears with invalid username |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `fakeuser123`, Password: `secret_sauce` |

**Steps:**
1. Enter invalid username `fakeuser123`
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** Error message: "Epic sadface: Username and password do not match any user in this service"  
**Actual Result:** Correct error message appears  
**Status:** ✅ PASS

---

### TC-08: Login with invalid password

| Field | Value |
|-------|-------|
| **Description** | Verify error appears with invalid password |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `standard_user`, Password: `wrongpassword` |

**Steps:**
1. Enter username `standard_user`
2. Enter invalid password `wrongpassword`
3. Click "Login" button

**Expected Result:** Error message about invalid credentials  
**Actual Result:** Error message appears as expected  
**Status:** ✅ PASS

---


