# Bug Report - Login Functionality

## Bug Details

### BUG-01: Performance issue with "performance_glitch_user"

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Priority** | Medium |
| **Status** | Open |
| **Module** | Login |
| **Reported By** | Zahid Solorzano |

**Title:** Login takes approximately 5 seconds for performance_glitch_user

**Description:**  
When logging in with `performance_glitch_user`, the redirect to the products page takes significantly longer than other users (approximately 5 seconds vs <1 second for standard_user).

**Steps to Reproduce:**
1. Go to login page (https://www.saucedemo.com)
2. Enter username: `performance_glitch_user`
3. Enter password: `secret_sauce`
4. Click "Login" button
5. Observe loading time

**Expected Result:**  
Page redirects within 1-2 seconds, similar to standard_user

**Actual Result:**  
Page takes approximately 5 seconds to redirect after clicking login

**Evidence:**  
![performance-bug](./evidence/performance-glitch-timing.png)

**Additional Info:**  
This may be intentional (simulating a user with network issues), but worth documenting.

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


