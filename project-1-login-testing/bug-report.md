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

### BUG-02: No visual loading indicator during slow login

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Priority** | Low |
| **Status** | Open |
| **Module** | Login (UI/UX) |
| **Reported By** | Zahid Solorzano |

**Title:** No loading spinner or visual feedback during login redirection

**Description:**  
When login takes more than 1 second (especially with performance_glitch_user), there is no visual indicator that something is happening. The user sees no spinner, progress bar, or loading message.

**Steps to Reproduce:**
1. Login with `performance_glitch_user`
2. Click login button
3. Observe the screen during the 5-second delay

**Expected Result:**  
A loading indicator (spinner, "Loading...", or progress bar) should appear to inform the user that the request is being processed

**Actual Result:**  
The login button becomes unresponsive, but no visual feedback indicates that the system is working

**Evidence:**  
![no-loading-indicator](./evidence/no-loading-spinner.png)


