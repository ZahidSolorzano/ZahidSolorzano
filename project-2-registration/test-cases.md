# Test Cases - Login Functionality

### TC-01: Valid submit by filling all fields

**Description:** User can successfully submit the registration form by filling all available fields with valid data.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "elcapuleto705@gmail.com"
- Gender: "Male"
- Mobile: "4521690129"
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
3. Enter Email: "elcapuleto705@gmail.com"
4. Select Gender: "Male"
5. Enter Mobile: "4431234567"
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
- Mobile: "4521690129"
- Date of Birth: "12 Oct 2000"


**Steps:**
1. Enter First Name: "Carlos"
2. Enter Last Name: "Solorzano"
3. Select Gender: "Male"
4. Enter Mobile: "4431234567"
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
1. Click Submit button

**Expected Result:** 
- Form not submited
- Required fields appear with a red outline

**Actual Result:** 
- Form not submited
- Required fields appear with a red outlinen

**Status:** PASS

---

### TC-04: Character limit validation on all text fields

**Description:**  Verify if text fields  have character limits or handle long input correctly.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos" then 500 times"s"
- Last Name: "Solorzano" then 500 times "o"
- Email: "elcapuleto705@gmail.com" then 500 times "m"
- Gender: "Male"
- Date of Birth: "12 Oct 2000"
- Mobile: "1234567890" then 500 times "0"  
- Address: "Marquesa 35" then 500 times "a"

**Steps:**
1. Enter First Name: "Carlos" then continuously press "s"
2. Enter Last Name: "Solorzano"  then continuously press "o"
3. Enter Email "elcapuleto705@gmail.com" then continuously press "m"
4. Select Gender: "Male"
5. Enter Mobile: "1234567890" then continuously press "0"
6. Enter Date of Birth: "12 Oct 2000"
7. Enter Address marquesa 35 then continuously press "a"
8. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, or truncate automatically, or show a limit warning

**Actual Result:**
- Mobile only accepted the first 10 digits so the rest were ignored
- Email only accepted a maximon of 5 characters after the last dot
- First name, last name and address accepted more than 500 characters so there is no limit

**Status:** FAIL

---

### TC-05: Email field - character limit in the domain part (before .com)

**Description:** Verify that the Email field limits the number of characters allowed in the domain section. Between "@" and the last dot before ".com".

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- 60 characters then "@gmail.com"
- "test@" then 60 characters and then ".com"


**Steps:**
1. Enter valid data in all required fields
2. Enter Email with 60+ characters BEFORE the "@"
3. Click Submit and observe
4. Repeat with 60+ characters AFTER "@" but BEFORE ".com"
5. Click Submit and observe

**Expected Result:** 
The system must handle long input in some way like reject with an error message, or truncate automatically, or show a limit warning

**Actual Result:**
- Before "@" field has no character limit
- After "@" but before ".com" field has no character limit

**Status:** FAIL 

---


