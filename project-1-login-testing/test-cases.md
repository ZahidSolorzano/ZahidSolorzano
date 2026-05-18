# Test Cases - Login Functionality

**Application:** SauceDemo (https://www.saucedemo.com)  
**Tester:** Zahid Solorzano  
**Date:** 05/17/2026

---

## Test Environment

| Item | Details |
|------|---------|
| Browser | Chrome / Firefox / Edge (especifica el que usaste) |
| OS | Windows / Mac / Linux |
| Network | Internet connection |

---

## Test Cases Summary

| Total Test Cases | Passed | Failed | Blocked |
|-----------------|--------|--------|---------|
| 8 | 5 | 3 | 0 |

---

## Detailed Test Cases

### TC-01: Valid login with standard user

| Field | Value |
|-------|-------|
| **Description** | Verify user can login with valid credentials |
| **Preconditions** | User is on login page (https://www.saucedemo.com) |
| **Test Data** | Username: `standard_user`, Password: `secret_sauce` |

**Steps:**
1. Enter username `standard_user`
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** User is redirected to products page (inventory.html)  
**Actual Result:** User is redirected to products page  
**Status:** ✅ PASS

---

### TC-02: Valid login with problem user

| Field | Value |
|-------|-------|
| **Description** | Verify problem_user can login successfully |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `problem_user`, Password: `secret_sauce` |

**Steps:**
1. Enter username `problem_user`
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** User is redirected to products page  
**Actual Result:** User is redirected to products page  
**Status:** ✅ PASS

---

### TC-03: Valid login with performance glitch user

| Field | Value |
|-------|-------|
| **Description** | Verify performance_glitch_user can login (may have delay) |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `performance_glitch_user`, Password: `secret_sauce` |

**Steps:**
1. Enter username `performance_glitch_user`
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** User is redirected to products page (may take a few seconds)  
**Actual Result:** User is redirected to products page after ~5 second delay  
**Status:** ✅ PASS (with observation)

---

### TC-04: Login with locked out user (negative test)

| Field | Value |
|-------|-------|
| **Description** | Verify locked_out_user cannot login and sees error message |
| **Preconditions** | User is on login page |
| **Test Data** | Username: `locked_out_user`, Password: `secret_sauce` |

**Steps:**
1. Enter username `locked_out_user`
2. Enter password `secret_sauce`
3. Click "Login" button

**Expected Result:** Error message appears: "Epic sadface: Sorry, this user has been locked out."  
**Actual Result:** Error message appears as expected  
**Status:** ✅ PASS

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

## Test Execution Log

| Test Case | Status | Date | Tester |
|-----------|--------|------|--------|
| TC-01 | ✅ PASS | [date] | Zahid Solorzano |
| TC-02 | ✅ PASS | [date] | Zahid Solorzano |
| TC-03 | ✅ PASS | [date] | Zahid Solorzano |
| TC-04 | ✅ PASS | [date] | Zahid Solorzano |
| TC-05 | ✅ PASS | [date] | Zahid Solorzano |
| TC-06 | ✅ PASS | [date] | Zahid Solorzano |
| TC-07 | ✅ PASS | [date] | Zahid Solorzano |
| TC-08 | ✅ PASS | [date] | Zahid Solorzano |

---

## Notes & Observations

- TC-03 (performance_glitch_user) takes approximately 5 seconds to redirect. This could be considered a performance issue.
- All error messages are consistent and user-friendly.
- The login page is simple and intuitive.
