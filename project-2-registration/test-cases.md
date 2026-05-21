# Test Cases - Login Functionality

## TC-01: Valid submit by filling all fields (required + optional)

**Description:** User can successfully submit the registration form by filling all available fields with valid data.

**Preconditions:** User has navigated to https://demoqa.com/automation-practice-form

**Test Data:**
- First Name: "Carlos"
- Last Name: "Solorzano"
- Email: "elcapuleto705@gmail.com"
- Gender: "Male"
- Mobile: "4431234567"
- Date of Birth: "12 Oct 2000"
- Subjects: "Computer Science"
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
7. Enter Subjects: "Computer Science" (press Enter)
8. Select Hobby: "Reading"
9. Upload Picture: "example.png"
10. Enter Current Address: "Marquesa 35"
11. Select State: "NCR"
12. Select City: "Delhi"
13. Click Submit button

**Expected Result:** 
- Form is submitted successfully
- A modal window appears showing the summary of ALL entered data (both required and optional fields)

**Actual Result:** 
- Form submitted successfully
- Summary modal displayed with all data correctly shown

**Status:** PASS

