# Test Cases - Airport Gap Functionality

## Setup: Obtaining an Authentication Token

**Steps to get and add the token:**

1. Go to https://airportgap.com
2. Click Generate Token
3. Enter Email "examplezahid@test.com"
4. Enter Password "portfolio"
5. Copy provided token (32MAXs3SX4oHVmWc5L74fbcG)
6. In Postman, in the authentication tab
   - Auth type: `Bearer Token`
   - Token: `32MAXs3SX4oHVmWc5L74fbcG`

**Note:** For all test cases below, "valid Bearer token" refers to the token obtained using these steps.

### API-TC-01: Gets all airports

| Field | Value |
|----|----|
|**Description** | Verify that GET /airports returns a list of airports |
|**Preconditions** |  Open Postman |
| **Test Data** | Method: GET<br>Url: https://airportgap.com/api/airports |
|**Steps** | 1. Open Postman <br> 2. Create a new collection named "Airports" <br> 3. Create a new request <br> 4. Set method to GET <br> 5. Enter URL: https://airportgap.com/api/airports <br> 6. Click Send |
| **Expected Result** | Status 200 OK <br> Response has data array <br> Each airport has id, type, attributes |
| **Actual Result** | Status 200, array of airports | 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-02: Get a specific airport

| Field | Value |
|----|----|
|**Description** | Verify GET /airports/GKA returns Goroka Airport |
|**Preconditions** |  Open Postman <br> Open collection "Airports" |
| **Test Data** | Method: GET<br> Url: https://airportgap.com/api/airports/GKA |
|**Steps** | 1. Open Postman <br> 2. Create a new request <br> 3. Set method to GET <br> 4. Enter URL: https://airportgap.com/api/airports/GKA <br> 5. Click Send |
| **Expected Result** | Status 200 <br> Airport id = "GKA" <br> Name contains "Goroka" |
| **Actual Result** | Status 200, id=GKA, name="Goroka Airport"| 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-03: Airport not found

| Field | Value |
|----|----|
|**Description** | Verify GET with invalid airport ID returns 404 |
|**Preconditions** |  Open Postman <br> Open collection "Airports" |
| **Test Data**  |Method: GET<br>Url: https://airportgap.com/api/airports/XYZ123 |
|**Steps** | 1. Create a new request <br> 2. Set method to GET <br> 3. Enter URL: https://airportgap.com/api/airports/XYZ123 <br> 4. Click Send|
| **Expected Result** | Status 404 Not Found |
| **Actual Result** | Status 404 Not Found| 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-04: Add airport to favorites

| Field | Value |
|-------|-------|
| **Description** | Verify user can add an airport to favorites |
| **Preconditions** | Open Postman <br> Open collection "Airports" <br> User has no existing favorites or the test airport is not in favorites <br> Authentication is already configured with the valid Bearer Token |
| **Test Data** | Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "airport_id": "GKA" } |
| **Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In body tab select raw, select JSON and enter: { "airport_id": "GKA" } <br> 5. Click Send |
| **Expected Result** | Status 201, favorite created |
| **Actual Result** | Status 201 Created <br> Response includes the favorite airport |
| **Status** | PASS |
| **Priority** | HIGH |


### API-TC-05: Favorites - Add duplicate airport

| Field | Value |
|----|----|
|**Description** | 	Verify adding same airport twice returns error |
|**Preconditions** |  Open Postman <br> Open collection "Airports" <br> User has already added aiport "GKA" to the favorites list <br> Authentication is already configured with the valid Bearer Token |
| **Test Data**  |Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "airport_id": "GKA" }|
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In body tab select raw, select JSON and enter: { "airport_id": "GKA" } <br> 5. Click Send |
| **Expected Result** | Status 422, error message |
| **Actual Result** | Status 422 Unprocessable Entity <br> Error: airport already in favorites| 
| **Status** | PASS |
| **Priority** | HIGH  |

### API-TC-06: Favorites — Missing airport_id

| Field | Value |
|----|----|
|**Description** | 	Verify error when airport_id is missing |
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: {  }|
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In body tab select raw, select JSON and enter: { } <br> 5. Click Send |
| **Expected Result** | 422 Unprocessable Entity|
| **Actual Result** | Status 422 "Airport Please enter a valid airport code"| 
| **Status** | PASS |
| **Priority** | MEDIUM |


### API-TC-07: Favorites — Wrong airport_id

| Field | Value |
|----|----|
|**Description** | 	Verify error when airport_id is invalid|
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "airport_id": "999" }|
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In body tab select raw, select JSON and enter: { "airport_id": "999" } <br> 5. Click Send |
| **Expected Result** | 422 Unprocessable Entity|
| **Actual Result** | Status 422 "Airport Please enter a valid airport code"| 
| **Status** | PASS |
| **Priority** |  MEDIUM |

### API-TC-08: Favorites - Wrong field name
| Field | Value |
|----|----|
|**Description** | 	Verify that the API returns a clear error message when the request body uses an incorrect field name instead of "airport_id" |
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "wrong_field": "GKA" }|
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In body tab select raw, select JSON an enter: { "wrong_field": "GKA" } <br> 5. Click Send |
| **Expected Result** | Status code: 422 Unprocessable Entity <br> Error message should clearly indicate that the field 'airport_id' is required and is missing|
| **Actual Result** | Error message: "Airport Please enter a valid airport code"| 
| **Status** |  FAIL |
| **Priority** | LOW |
| **Bug reference** | API-BUG-01 |

### API-TC-09: Get my favorite airports

| Field | Value |
|----|----|
|**Description** | Verify GET /favorites returns list of user's favorite airports |
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> User has already added at least one airport to the favorites list <br> Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: GET <br> Url: https://airportgap.com/api/favorites |
|**Steps** | 1. Create a new request <br> 2. Set method to GET <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. Click Send|
| **Expected Result** | Status 200, Response includes previously added favorites|
| **Actual Result** | Status 200, includes GKA | 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-10: Remove favorite

| Field | Value |
|----|----|
|**Description** | Verify user can delete a favorite airport |
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> User has already added at least one airport to the favorites list <br>Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: DELETE <br> Url: https://airportgap.com/api/favorites/GKA |
|**Steps** | 1. Create a new request <br> 2. Set method to DELETE <br> 3. Enter URL:https://airportgap.com/api/favorites/GKA <br> 4. Click Send|
| **Expected Result** | Status 204 No Content|
| **Actual Result** | Status 204 | 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-11: Delete non-existent favorite

| Field | Value |
|----|----|
|**Description** | Verify error when deleting a favorite that doesn't exist |
|**Preconditions** |  Open Postman<br> Open collection "Airports" <br> Authentication is already configured with the valid Bearer Token|
| **Test Data**  |Method: DELETE <br> Url: https://airportgap.com/api/favorites/999|
|**Steps** | 1. Create a new request <br> 2. Set method to GET <br> 3. Enter URL: https://airportgap.com/api/favorites/999 <br> 4. Click Send|
| **Expected Result** | Status 404 Not Found |
| **Actual Result** | Status 404 | 
| **Status** | PASS |
| **Priority** | MEDIUM |

### API-TC-12: Request without authentication token

| Field | Value |
|----|----|
|**Description** | Verify API rejects requests without valid token |
|**Preconditions** |  Open Postman <br> Open collection "Airports" |
| **Test Data**  |Method: GET <br> Url: https://airportgap.com/api/favorites |
|**Steps** | 1. Create a new request <br> 2. Set method to GET <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. Click Send|
| **Expected Result** | Status 401 Unauthorized|
| **Actual Result** | Status 401, error message| 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-13: Calculate distance

| Field | Value |
|----|----|
|**Description** | Verify distance between two airports is calculated correctly |
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  |Method: POST <br>  Url: https://airportgap.com/api/airports/distance?from=GKA&to=MAG |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA&to=MAG <br> 4. Click Send |
| **Expected Result** | Status 200,  distance calculated |
| **Actual Result** | Status 200, Response includes distance in miles and kilometers| 
| **Status** | PASS |
| **Priority** | HIGH |

### API-TC-14: Distance - Missing "to" parameter
| Field | Value |
|----|----|
|**Description** | Verify that the API returns an error when the required "to" parameter is missing from the distance request |
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  |Method: POST <br>  Url: https://airportgap.com/api/airports/distance?from=GKA |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA <br> 4. Click Send |
| **Expected Result** | "Missing required parameter: to" |
| **Actual Result** | "Please enter valid 'from' and 'to' airports"| 
| **Status** | FAIL |
| **Priority** | MEDIUM |
| **Bug reference** | API-BUG-02 |

### API-TC-15:  Distance - Missing "from" parameter

| Field | Value |
|----|----|
|**Description** |  Verify that the API returns an error when the required "from" parameter is missing from the distance request |
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  |Method: POST <br>  Url: https://airportgap.com/api/airports/distance?to=GKA |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?to=GKA <br> 4. Click Send |
| **Expected Result** | "Missing required parameter: from" |
| **Actual Result** | "Please enter valid 'from' and 'to' airports"| 
| **Status** | FAIL |
| **Priority** | MEDIUM |
| **Bug reference** | API-BUG-03 |

### API-TC-16: Distance with the same airport as origin and destination

| Field | Value |
|-------|-------|
| **Description** | Verify behavior when requesting distance between the same airport |
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  | Method: POST <br>  Url: https://airportgap.com/api/airports/distance?from=GKA&to=GKA |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA&to=GKA <br> 4. Click Send |
| **Expected Result** | 200 OK with distance = 0  |
| **Actual Result** | 200 OK with distance = 0 |
| **Status** | PASS |
| **Priority** | LOW |

### API-TC-17: Distance with invalid airport code (in "to" parameter)

| Field | Value |
|-------|-------|
| **Description** | Verify that requesting distance with a non-existent airport code returns appropriate error|
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  | Method: POST <br>  Url: https://airportgap.com/api/airports/distance?from=GKA&to=999 |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA&to=999 <br> 4. Click Send |
| **Expected Result** | Error message should specify which parameter is invalid |
| **Actual Result** | "422 state, Generic message: "Please enter valid 'from' and 'to' airports"|
| **Status** | FAIL |
| **Priority** | MEDIUM |
| **Bug reference** | API-BUG-05 |


### API-TC-18: Distance with invalid airport code (in "from" parameter)

| Field | Value |
|-------|-------|
| **Description** | Verify that requesting distance with a non-existent airport code returns appropriate error|
|**Preconditions** |  Open Postman <br>  Open collection "Airports" |
| **Test Data**  | Method: POST <br>  Url: https://airportgap.com/api/airports/distance?from=999&to=GKA |
|**Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=999&to=GKA <br> 4. Click Send |
| **Expected Result** | Error message should specify which parameter is invalid |
| **Actual Result** | "422 state, Generic message: "Please enter valid 'from' and 'to' airports"|
| **Status** | FAIL |
| **Priority** | MEDIUM |
| **Bug reference** | API-BUG-04 |

