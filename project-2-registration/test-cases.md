# Test Cases - Student Form Functionality

## Default Test Data

For all test cases unless specified otherwise:

| Field | Value |
|-------|-------|
| First Name | "Carlos" |
| Last Name | "Solorzano" |
| Gender | "Male" |
| Mobile | "1234567890" |
| Date of Birth | "12 Oct 2000" |

### TC-01: Valid submit by filling all fields

**Description:** User can successfully submit the registration form by filling all available fields with valid data.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "carlostest@example.com"
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
3. Enter Email: "carlostest@example.com"
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
- Summary window displayed with all data correctly shown

**Status:** PASS

---

### TC-02: Submit with all fields empty

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
- Required fields are highlighted with a red outline (first name, last name, gender, mobile abd date of birt)
- Optional fields are highlighted with a green outline (email, subjects, hobbies, picture, address, city and state)

**Status:** PASS

---

### TC-03: Valid submit by filling only the required fields

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
- Summary window displayed with all data correctly shown
- Optional fields are highlighted with a green outline (email, subjects, hobbies, picture, address, city and state)

**Status:** PASS

---

### TC-04: First Name - Character limit 

**Description:** Verify that the First Name field has a character limit or handles very long input appropriately

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:**
- First Name: 256 characters: "A" repeated 256 times

**Steps:**
1. Enter valid data in all other required fields
2. Enter 256 characters in First Name field (type "A" 256 times)
3. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- First name field accepted very long text without any error

**Status:** FAIL

---

### TC-05: First Name - Rejects numeric characters

**Description:** Verify First Name field rejects input containing numbers

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:**
- First Name: "Carlos123"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Carlos123" in First Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- First name field is highlighted with a red outline

**Actual Result:** 
- Form is submited
- First name allows numeric characters.

**Status:** FAIL

---

### TC-06: First Name - Reject special characters

**Description:** Verify that First Name field rejects input containing special characters

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:**
- First Name: "Carlos@#$"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Carlos@#$" in First Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- First name field is highlighted with a red outline

**Actual Result:** 
- Form is submited
- First name allows special characters.

**Status:** PASS

---

### TC-07: Last Name - Character limit

**Description:** Verify that the Last Name field has a character limit or handles very long input appropriately

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
-  All other required fields filled with valid defaults

**Test Data:** Last Name: 256 characters: "B" repeated 256 times

**Steps:**
1. Enter valid data in all other required fields
2. Enter 256 characters in Last Name field (type "B" 256 times)
3. Click Submit button

**Expected Result:** 
The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- Last name field accepted very long text without any error

**Status:** FAIL

---

### TC-08: Last Name  - Rejects numeric characters

**Description:** Verify that Last Name field rejects input containing numbers

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Last Name: "Solorzano123"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Solorzano123" in Last Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- Last name field is highlighted with a red outline

**Actual Result:** 
- Form is submited
- Last name allows numeric characters.

**Status:** FAIL

---

### TC-09: Last Name - Rejects special characters

**Description:** Verify Last Name field rejects input containing special characters

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Last Name: "Solorzano@#$"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Solorzano@#$" in Last Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- Last name field is highlighted with a red outline

**Actual Result:** 
- Form is submited
- Last name field allows special characters.

**Status:** FAIL

### TC-10 Email - Charcater limit in the local part

**Description:** Verify that the Email field has a character limit or handles very long input appropriately in the local part(before the @)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: (65 times "A") + "@example.com"

**Steps:**
- Enter valid data in all required fields
- Enter 65 characters in Email fiel (type "A" 76 times before the @)
- Click Submit button

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:** 
- Form is submited
- Email field has no character limit in the local part

### TC-11 Email - Charcater limit in the domain part

**Description:** Verify that the Email field has a character limit or handles very long input appropriately in the domain part (after the @ and beforer the last dot)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data** Email:  + "carlostest@" + (65 times "A") + ".com"

**Steps:**
- Enter valid data in all required fields 
- Enter "carlostest@" + (type 65 times "A") + ".com" in the email field
- Click Submit button

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:** 
- Form is submited
- Email field has no character limit in the domain part

**Status:** PASS

### TC-12 Email - Charcater limit in the top level domain part

**Description:** Verify that the Email field has a character limit or handles very long input appropriately in the top level domain part (afterthe last dot)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data**
- Email: "carlostest@example." + (65 times "A")

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest@example." + (type 65 times "A") in the email field
3. Click Submit button

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:** 
- Email field gets highlighted with a red outline
- Email field has a 5 character limit in the top level domain part

**Status:** PASS

---

### TC-13 Email -  Top level domain part has a minumum lenght of 2 character

**Description:** Verify that the Email field in the Top level domain part has a minumum lenght of 2 character (like .us or .mx)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data**
- Email: "carlostest@example.m" 

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest@example.m"
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- Email field gets highlighted with a red outline

**Actual Result:** 
- Email field gets highlighted with a red outline
- Email field has a 2 character minimun in the top level domain part
- Form cannot be submited

**Status:** PASS

---

### TC-13 Email without the local part

**Description:** Verify that the Email requires to have a valid local part (before the @)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: "@example.com" 

**Steps:**
1. Enter valid data in all required fields
2. Enter "@example.com" in the email field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit.

**Actual Result:** 
- Email field gets highlighted with a red outline
- Form cannot be submited

**Status:** PASS

---

### TC-10: User can select only one gender

**Description:** Verify that the gender field can be selected with one gender at a time.

**Preconditions:** Navigate to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano@#$"
- Gender: "Male"
- Mobile: "1234567890"
- Date of Birth: "12 Oct 2000"

**Steps:**
1. Enter valid data in all required fields
2. Select "Male" Gender
3. Try to select a second Gender "Female"
4. Click Submit button

**Expected Result:**
-System should only allow user to highligh one gender of the gender list

**Actual Result:**
-System only highlights the last gender that the user clicked

### TC-11: Mobile - Lenght (More than 10 digits)

**Description:**  Verify Mobile field only accepts exactly 10 digits and ignores extra digits

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Date of Birth: "12 Oct 2000"
- Mobile: "12345678901"

**Steps:**
1. Enter valid data in all required fields (First Name, Last Name, Gender and Date of Birth)
2. Enter  "12345678901" in Mobile field
3. Click Submit button

**Expected Result:** 
-Should either truncate to 10 digits or show error message

**Actual Result:**
Shows mobile field highlighted with a red outline and form cannot be submitted

**Status:** PASS

---
### TC-11: Mobile - Lenght (More than 10 digits)

**Description:**  Verify Mobile field only accepts exactly 10 digits and ignores extra digits

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Date of Birth: "12 Oct 2000"
- Mobile: "12345678901"

**Steps:**
1. Enter valid data in all required fields (First Name, Last Name, Gender and Date of Birth)
2. Enter  "12345678901" in Mobile field
3. Click Submit button

**Expected Result:** 
-Should either truncate to 10 digits or show error message

**Actual Result:**
Shows mobile field highlighted with a red outline and form cannot be submitted

**Status:** PASS

---

### TC-11: Mobile - Lenght Less than 10 digits)

**Description:**  Verify Mobile field only accepts exactly 10 digits 

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Gender: "Male"
- Date of Birth: "12 Oct 2000"
- Mobile: "123456789"

**Steps:**
1. Enter valid data in all required fields (First Name, Last Name, Gender and Date of Birth)
2. Enter  "123456789" in Mobile field
3. Click Submit button

**Expected Result:** 
-Should show error message or hightligt the field

**Actual Result:**
Shows mobile field highlighted with a red outline and form cannot be submitted

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




