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

---

### TC-01: Valid submit by filling all fields

**Description:** User can successfully submit the registration form by filling all available fields with valid data.

**Preconditions:** User has navigated to the practice form (https://demoqa.com/automation-practice-form)

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

**Status:** PASS | **Priority:** HIGH

---

### TC-02: Submit with all fields empty

**Description:** User cannot successfully submit the registration form by leaving in blank all the fields.

**Preconditions:** User has navigated to the practice formm

**Steps:**
1. Do not enter any data in any field
2. Click Submit button

**Expected Result:** 
- Form not submited
- Required fields are highlighted with a red outline

**Actual Result:** 
- Form not submited
- Required fields are highlighted with a red outline (first name, last name, gender, mobile and date of birt)
- Optional fields are highlighted with a green outline (email, subjects, hobbies, picture, address, city and state)

**Status:** PASS | **Priority:** HIGH

---

### TC-03: Valid submit by filling only the required fields

**Description:** User can successfully submit the registration form by filling only the required fields with valid data.

**Preconditions:** User has navigated to the practice form

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
- A modal window appears showing the summary of all entered data 

**Actual Result:** 
- Form submitted successfully
- Summary window displayed with all data correctly shown
- Optional fields are highlighted with a green outline (email, subjects, hobbies, picture, address, city and state)

**Status:** PASS | **Priority:** HIGH

---

### TC-04: First Name - Character limit 

**Description:** Verify that the First Name field has a character limit or handles very long input appropriately

**Preconditions:** 
- User has navigated to the practice form
- All other required fields filled with valid defaults

**Test Data:** First Name: 256 characters: "A" repeated 256 times

**Steps:**
1. Enter valid data in all other required fields
2. Enter 256 characters in First Name field (type "A" 256 times)
3. Click Submit button

**Expected Result:**  The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- First name field accepted very long text without any error

**Status:** FAIL | **Priority:** LOW | **Bug reference:** BUG-01

---

### TC-05: First Name - Rejects numeric characters

**Description:** Verify First Name field rejects input containing numbers

**Preconditions:** 
- User has navigated to the practice form 
- All other required fields filled with valid defaults

**Test Data:** First Name: "Carlos123"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Carlos123" in First Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- First name field is highlighted with a red outline

**Actual Result:** 
- Form is submitted successfully
- First name field allows numeric characters.

**Status:** FAIL | **Priority:** MEDIUM | **Bug Reference** BUG-02

---

### TC-06: First Name - Reject special characters

**Description:** Verify that First Name field rejects input containing special characters

**Preconditions:** 
- User has navigated to the practice form 
- All other required fields filled with valid defaults

**Test Data:** First Name: "Carlos@#$"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "Carlos@#$" in First Name field
3. Click Submit button

**Expected Result:** 
- Form should not submit.
- First name field is highlighted with a red outline

**Actual Result:** 
- Form is submitted successfully
- First name allows special characters.

**Status:** FAIL | **Priority:** MEDIUM | **Bug Reference** BUG-03

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

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- Last name field accepted very long text without any error

**Status:** FAIL  **Priority:** LOW

---

### TC-08: Last Name - Rejects numeric characters

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
- Form submitted successfully
- Last name allows numeric characters.

**Status:** FAIL  **Priority:**  MEDIUM

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
- Form submitted successfully
- Last name field allows special characters.

**Status:** FAIL  **Priority:**  MEDIUM

### TC-10 Email - Character limit in the local part

**Description:** Verify that the Email field has a character limit or handles very long input appropriately in the local part(before the @)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: (65 times "A") + "@example.com"

**Steps:**
- Enter valid data in all required fields
- Enter 65 characters in Email fiel (type "A" 65 times before the @)
- Click Submit button

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:** 
- Form submitted successfully
- Email field has no character limit in the local part

**Status:** FAIL **Priority:** LOW 

### TC-11 Email - Charcater limit in the domain part

**Description:** Verify that the Email field has a character limit or handles very long input appropriately in the domain part (after the @ and before the last dot)

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
- Form submitted successfully
- Email field has no character limit in the domain part

**Status:** FAIL  **Priority:** LOW 

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
- Form cannot be submited

**Status:** PASS **Priority:** LOW 

---

### TC-13 Email - Top level domain part has a minumum lenght of 2 character

**Description:** Verify that the Email field in the Top level domain part has a minimum length of 2 character (like .us or .mx)

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
- Email field has a 2 character minimum length in the top level domain part
- Form cannot be submited

**Status:** PASS **Priority:** LOW 

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

**Status:** PASS **Priority:** HIGH

---
### TC-14 Email without the domain part

**Description:** Verify that the Email requires to have a valid domain part (after the @ before the dot)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: "carlostest@.com" 

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest@.com" in the email field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit.

**Actual Result:** 
- Email field gets highlighted with a red outline
- Form cannot be submited

**Status:** PASS **Priority:** HIGH

---

### TC-15  Email without the top level domain part

**Description:** Verify that the Email requires to have a valid domain part (after thethe dot)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: "carlostest@example." 

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest@.com" in the email field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit.

**Actual Result:** 
- Email field gets highlighted with a red outline
- Form cannot be submited

**Status:** PASS **Priority:** HIGH

---

### TC-16  Email rejects special characters

**Description:** Verify that the Email field rejects input containing special characters (besides "@" and ".")

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: "carlostest#$@example.com" 

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest#$@example.com" in the email field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit.

**Actual Result:** 
- Email field gets highlighted with a red outline
- Form cannot be submited

**Status:** PASS **Priority:** HIGH

---

### TC-17  Email rejects numerical characters in the top level domain part

**Description:** Verify that the Email field rejects input containing numerical characters in the top level domain part (after the dot)

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Email: "carlostest@example.com12" 

**Steps:**
1. Enter valid data in all required fields
2. Enter "carlostest@example.com12" in the email field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit.

**Actual Result:** 
- Email field gets highlighted with a red outline
- Form cannot be submited

**Status:** PASS **Priority:** MEDIUM

---

### TC-18: User can only select one gender

**Description:** Verify that the gender field can be selected with one gender at a time.

**Preconditions:** 
- Navigate to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:**
- Gender: "Male"
- Second gender: "Female"

**Steps:**
1. Enter valid data in all required fields
2. Select "Male" Gender
3. Try to select a second Gender "Female"
4. Click Submit button

**Expected Result:** 
- System should only allow user to highligh one gender of the gender list
- Form should not be submited

**Actual Result:** System only highlights the last gender that the user clicked

**Status:** PASS **Priority:** HIGH

---

### TC-19: Mobile - Lenght (More than 10 digits)

**Description:**  Verify Mobile field only accepts exactly 10 digits and ignores extra digits

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Mobile: "12345678901"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "12345678901" in Mobile field
3. Click Submit button

**Expected Result:** The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:** 
- Shows mobile field highlighted with a red outline and
- Form cannot be submitted

**Status:** PASS **Priority:** HIGH

---
### TC-20: Mobile - Lenght (Less than 10 digits)

**Description:**  Verify Mobile field only accepts exactly 10 digits 

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Mobile: "123456789"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "123456789" in Mobile field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit

**Actual Result:** 
- Shows mobile field highlighted with a red outline 
- Form cannot be submitted

**Status:** PASS **Priority:** HIGH

---

### TC-21: Mobile field rejects non numerical character

**Description:**  Verify Mobile field only accepts numerical characters and ignores non numerical character

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Mobile: "123456789A"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "123456789A" in Mobile field
3. Click Submit button

**Expected Result:** 
- Email field gets highlighted with a red outline
- Form should not submit

**Actual Result:** 
- Shows mobile field highlighted with a red outline 
- Form cannot be submitted

**Status:** PASS **Priority:** HIGH

---

### TC-22: Mobile field rejects special characters

**Description:**  Verify Mobile field only accepts numerical characters and ignores special characters

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Mobile: "123456789@"

**Steps:**
1. Enter valid data in all other required fields
2. Enter "123456789@" in Mobile field
3. Click Submit button

**Expected Result:** 
- Mobile field gets highlighted with a red outline
- Form should not submit

**Actual Result:** 
- Mobile field gets highlighted with a red outline
- Form cannot be submitted

**Status:** PASS **Priority:** HIGH

---

### TC-23: Date of birth field rejects future dates

**Description:**  Verify Date of Birth field rejects future dates

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Date of birt: 05/24/2026

**Steps:**
1. Enter valid data in all other required fields
2. Select "05/24/2026" in Date of birt field
3. Click Submit button

**Expected Result:** 
- Date of birth field gets highlighted with a red outline
- Form should not submit

**Actual Result:** 
- Form submitted successfully
- Date of birth field allows future dates

**Status:** FAIL **Priority:** HIGH

---

### TC-24: User can select multiple subjects

**Description:**  Verify that the user is allowed to select multiple subject

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Subjects: English, Maths, Civics and Arts

**Steps:**
1. Enter valid data in all other required fields
2. Select the subjects English, Maths, Civics and Arts
3. Click Submit button

**Expected Result:** +
- Form should be submited
- User is allowed to select multiple subject

**Actual Result:** 
- Form submitted successfully
- User is allowed to select multiple subjects

**Status:** PASS **Priority:** HIGH

---

### TC-25: User cannot select a non existing subject

**Description:**  Verify that the user is not allowed to select a subject that is not on the database

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All other required fields filled with valid defaults

**Test Data:** Subjects: Astrology

**Steps:**
1. Enter valid data in all other required fields
2. Enter Astrology in the subject field
3. Click Submit button

**Expected Result:** 
- Subject field gets highlighted with a red outline
- Form should not submit if there is a non existing subject selected

**Actual Result:**
- No dropdown options appear for "Astrology"
- The text disappears when clicking outside
- Field becomes blank
- Form submitted successfully

**Status:** PASS **Priority:** HIGH

---

### TC-26: User can select multiple hobbies

**Description:**  Verify that the user is allowed to select multiple hobbies

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Hobbies: Sports, Reading, Music

**Steps:**
1. Enter valid data in allrequired fields
2. Click the box for all the hobbies available on the form (Sports, Reading, Music)
3. Click Submit button

**Expected Result:** 
- User is allowed to select multiple hobbies
- Form should be submited

**Actual Result:** 
- User is allowed to select multiple hobbies
- Form submitted successfully

**Status:** PASS **Priority:** HIGH

---

### TC-27: Picture field can only allow image formats

**Description:**  Verify that the picture field can only be used to upload images
**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Picture: example.mp4

**Steps:**
1. Enter valid data in allrequired fields
2. Upload example.mp4 in the picture field
3. Click Submit button

**Expected Result:** 
- Picture field is highlighted with a red outline or shows a warning message
- Form should not be submited

**Actual Result:** 
- User is allowed to upload non image files in the picture field
- Form submitted successfully

**Status:** FAIL **Priority:** HIGH

---

### TC-28: Picture field allow different image formats

**Description:**  Verify that the picture field can be used to upload images in diferent formats (jpg, png, gif, webm, etc)
**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Picture: cat.gif

**Steps:**
1. Enter valid data in allrequired fields
2. Upload cat.gif in the picture field
3. Click Submit button

**Expected Result:** 
- User is allowed to upload images with diferent formats
- Form should be submited

**Actual Result:** 
- User is allowed to upload images with diferent formats
- Form submitted successfully

**Status:** PASS **Priority:** HIGH

---

### TC-29: Address - Character limit 

**Description:** Verify that the address field has a character limit or handles very long input appropriately

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** Address: Type "C" 500 times

**Steps:**
1. Enter valid data in all required fields
2. Enter 500 characters in address field (type "C" 500 times)
3. Click Submit button

**Expected Result:**  The system must handle long input in some way like reject with an error message, truncate automatically, or show a limit warning

**Actual Result:**
- Form submitted successfully
- Address field accepted very long text without any error

**Status:** FAIL **Priority:** LOW

---
### TC-30: User can select a State without selecting a city

**Description:**  Since the City and State field are consider optional, the user should be able to select a state and ignore the city field

**Preconditions:** 
- User has navigated to https://demoqa.com/automation-practice-form
- All required fields filled with valid defaults

**Test Data:** State: "NCR"

**Steps:**
1. Enter valid data in all required fields
2. Select "NCR" in the State field
3. Click Submit button

**Expected Result:**  
- Form submitted successfully

**Actual Result:**
- Form submitted successfully
- Summary window does not show the selected State

**Status:** FAIL **Priority:** MEDIUM

---

### TC-31: Summary window can be closed

**Description:**  Once the summary window appear it should be able to be closed by clicking the "Close" button

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
7. Click Close button

**Expected Result:**  
- Form submitted successfully
- Summary window can be closed by clicking the "Close" Button

**Actual Result:**
- Form submitted successfully
- Summary window cannot get closed by cliking the "Close" Button
- Summary window can only get closed by clicking anywhere outside the window.

**Status:** FAIL **Priority:** HIGH

---



