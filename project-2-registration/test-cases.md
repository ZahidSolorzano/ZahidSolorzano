# Test Cases - Login Functionality

### TC-01: Valid submit by filling all fields

**Description:** User can successfully submit the registration form by filling all available fields with valid data.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "elcapuleto705@gmail.com"
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"
- Subjects: "Maths"
- Hobbies: "Reading"
- Picture: "example.png"
- Current Address: "Marquesa 35"
- State: "NCR"
- City: "Delhi"

**Steps:**
1. Enter First Name: "Carlos"
2. Enter Last Name: "Solorzano"
3. Enter Email: "carlos.test@example.com"
4. Select Gender: "Male"
5. Enter Mobile: "1234567890"
6. Enter Date of Birth: "12 Oct 2000"
7. Enter Subjects: "Maths"
8. Select Hobby: "Reading"
9. Upload Picture: "example.png"
10. Enter Current Address: "Marquesa 35"
11. Select State: "NCR"
12. Select City: "Delhi"
13. Click Submit button

**Expected Result:** 
- Form is submitted successfully
- A modal window appears showing the summary of ALL entered data 

**Actual Result:** 
- Form submitted successfully
- Summary modal displayed with all data correctly shown

**Status:** PASS

---

### TC-02: Valid submit by filling only the required fields

**Description:** User can successfully submit the registration form by filling only the required fields with valid data.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"


**Steps:**
1. Enter First Name: "Carlos"
2. Enter Last Name: "Solorzano"
3. Select Gender: "Male"
4. Enter Mobile: "1234567890"
5. Enter Date of Birth: "12 Oct 2000"
6. Click Submit button

**Expected Result:** 
- Form is submitted successfully
- A modal window appears showing the summary of ALL entered data 

**Actual Result:** 
- Form submitted successfully
- Summary modal displayed with all data correctly shown

**Status:** PASS

---

### TC-03: Invalid submit by not filling any of the fields

**Description:** User cannot successfully submit the registration form by leaving in blank all the fields.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Steps:**
1. Do not enter any data in any field
2. Click Submit button

**Expected Result:** 
- Form not submited
- Required fields are highlighted with a red outline

**Actual Result:** 
- Form not submited
- Required fields are highlighted with a red outline

**Status:** PASS

---

### TC-04a: Character limit - First Name

**Description:** Verify that the First Name field has a character limit or handles very long input appropriately

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: 256 characters: "A" repeated 256 times
- Last Name: "Solorzano"
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter valid data in all required fields ( Last Name, Gender, Mobile and Date of Birth)
2. Enter 256 characters in First Name field (type "A" 256 times)
3. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- First name field accepted very long text without any error

**Status:** FAIL

---

### TC-04b: Character limit - Last Name

**Description:** Verify that the Last Name field has a character limit or handles very long input appropriately

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: 256 characters: "B" repeated 256 times
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter valid data in all required fields (First Name, Gender, Mobile and Date of Birth)
2. Enter 256 characters in Last Name field (type "B" 256 times)
3. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- Last name field accepted very long text without any error

**Status:** FAIL

---
### TC-04c: Character limit - Mobile

**Description:** Verify Mobile field only accepts exactly 10 digits

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Date of Birth: "12 Oct 2000"
- Mobile: Less than 10 digits "123456789" 
- Mobile: Exactly 10 digits  "1234567890" 
- Mobile: More than 10 digits  "12345678901"

**Steps:**
1. Enter valid data in all required fields (First Name, Last Name, Gender and Date of Birth)
2. Enter the test data in Mobile field
3. Click Submit button

**Expected Result:** 
- Less than 10 digits: Should show validation error
- Exactly 10 digits: Should be accepted
- More than 10 digits: Should either truncate to 10 digits or show error message

**Actual Result:**
- Less than 10 digits: Shows red outline, form cannot be submitted
- Exactly 10 digits: Form submits successfully
- More than 10 digits: Shows red outline, form cannot be submitted

**Status:** PASS

---

### TC-04d: Character limit - Address

**Description:** Verify that the address field has a character limit or handles very long input appropriately

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter valid data in all required fields (First Name, Last Name, Gender, Mobile and Date of Birth)
2. Enter 500 characters in address field (type "C" 500 times)
3. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- Address field accepted very long text without any error

**Status:** FAIL

---

### TC-05a: Email field - Local part character limit

**Description:** Verify if the local part (before "@") has a character limit.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: (60 characters of letter "a")@example.com
- Gender: "Male"
- Mobile: "4431234567"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter all required fields with valid values (First Name, Last Name, Gender, Mobile and Date of Birth)
2. Enter Email with 60+ characters before the "@"
3. Click Submit button

**Expected Result:** 
Either accepts with truncation, or shows error message about character limit

**Actual Result:**
Form submitted successfully, local part accepted long text

**Status:** FAIL

---

### TC-05b: Email field - DOMAIN part character limit

**Description:** Verify if the domain part (between "@" and the last dot before extension) has a character limit.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "test@" +  (60 characters of letter "b") + ".com"
- Gender: "Male"
- Mobile: "4431234567"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter all fields required fields with  valid values
2. Enter Email with 60+ characters after "@" but BEFORE ".com"
3. Click Submit button

**Expected Result:** 
- Either accepts with truncation, OR Shows error message about character limit

**Actual Result:**
Form submitted successfully, domain part accepted long text.

**Status:** FAIL

---

### TC-05c: Email field -  character limit after the last dot

**Description:** Verify if the part after the last dot has a character limit.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "test@gmail.com" + (60+ 'c' characters)
- Gender: "Male"
- Mobile: "4431234567"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter all fields with valid values
2. Enter Email with 60+ characters AFTER ".com"
3. Click Submit button

**Expected Result:** 
Either accepts with truncation, or Shows error message about character limit

**Actual Result:**
- Form cannot be submited
- Email field only accepts a maximum of 5 characters after the last dot

**Status:** PASS 

---


