# Bug Report - Login Functionality

### BUG-01: Error message missing password requirement when both fields empty

| Field | Value |
|-------|-------|
| **Test Case** | TC-09: Login with all empty fields|
| **Description** | When both username and password fields are left empty and user clicks Login, the error message only mentions missing username. It does not mention that password is also required. |
| **Preconditions** | User is on login page (https://www.saucedemo.com) |
| **Steps** | 1. Leave username field empty <br> 2. Leave password field empty <br> 3. Click "Login" button |
| **Expected Result** | Error message should indicate both fields are required  |
| **Actual Result** | Error message appears: "Epic sadface: Username is required" |
| **Environment** | Windows 10, Google Chrome |
| **Severity** | Minor |
| **Priority** | Medium |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |
| **Evidence:** | ![empty-fields-error](./evidence/empty-fields-error.png) |


---


