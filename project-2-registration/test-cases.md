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

### TC-03: Invalid submit by not filling any of the fields

**Description:** User cannot successfully submit the registration form by leaving in blank all the fields.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Steps:**
1. Click Submit button

**Expected Result:** 
- Form not submited
- Required fields apear with a red outline

**Actual Result:** 
- Form not submited
- Required fields apear with a red outlinen

**Status:** PASS

### TC-04: Submiting form filling the fields with a big a mount of characters
