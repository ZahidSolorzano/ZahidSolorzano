# Bug Report - Login Functionality

### BUG-01: Performance issue with "performance_glitch_user"

| Field | Value |
|-------|-------|
|**Test Case** | TC-03: Valid login with performance glitch user |
| **Description** | When logging in with "performance_glitch_user", the redirect to the products page takes significantly longer than other users (approximately 5 seconds) |
| **Preconditions** | User is on login page |
| **Test Data** | Username: "performance_glitch_user", Password: "secret_sauce" |
| **Steps** | 1. Enter username "performance_glitch_user" <br> 2. Enter password "secret_sauce" <br> 3. Click "Login" button |
| **Expected Result** | User is redirected to products page |
| **Actual Result** | User is redirected to products page after a 5 second delay |
| **Module** | Login |
| **Severity** | Low |
| **Priority** | Medium |
| **Enviromet** | Windows 10 Desktop, Google Chrome |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |

---


