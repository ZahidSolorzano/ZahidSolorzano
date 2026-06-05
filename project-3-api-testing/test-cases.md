## Test Cases

### API-TC-01: GET /airports — Gets all airports

| Field | Value |
|----|----|
|**Description** | Verify that GET /airports returns a list of airports |
| **Method** | GET |
| **URL** | https://airportgap.com/api/airports |
|**Steps** | 1. Open Postman <br> 2. Create a new request <br> 3. Set method to GET <br> 4. Enter URL: https://airportgap.com/api/airports<br> 5. Click Send |
| **Headers** | 	Authorization: Token 32MAXs3SX4oHVmWc5L74fbcG |
| **Expected Result** | Status 200 OK <br> Response has data array <br> Each airport has id, type, attributes |
| **Actual Result** | Status 200, array of airports | 
| **Status** | PASS |

### API-TC-02: GET /airports/:id - Get a specific airport

| Field | Value |
|----|----|
|**Description** | Verify GET /airports/GKA returns Goroka Airport |
| **Method** | GET |
| **URL** | https://airportgap.com/api/airports/GKA |
|**Steps** | 1. Open Postman <br> 2. Create a new request <br> 3. Set method to GET <br> 4. Enter URL: https://airportgap.com/api/airports/GKA <br> 5. Click Send |
| **Expected Result** | Status 200 <br> Airport id = "GKA" <br> Name contains "Goroka" |
| **Actual Result** | Status 200, id=GKA, name="Goroka Airport"| 
| **Status** | PASS |

### API-TC-03: GET /airports/:id — Airport not found

| Field | Value |
|----|----|
|**Description** | Verify GET with invalid airport ID returns 404 |
| **Preconditions** | Open Postman|
| **Test Data**  |Method: GET, Url: https://airportgap.com/api/airports/XYZ123 |
|**Steps** | 1. Create a new request <br> 3. Set method to GET <br> 4. Enter URL: https://airportgap.com/api/airports/XYZ123 <br> 5. Click Send|
| **Expected Result** | Status 404 Not Found |
| **Actual Result** | Status 404 Not Found| 
| **Status** | PASS |

### API-TC-04: GET /airports/distance — Calculate distance

| Field | Value |
|----|----|
|**Description** | Verify distance between two airports is calculated correctly |
| **Preconditions** | Open Postman|
| **Test Data**  |Method: POST, Url: 	https://airportgap.com/api/airports/distance?from=GKA&to=MAG |
|**Steps** | 1. Create a new request <br> 3. Set method to POST <br> 4. Enter URL: 	https://airportgap.com/api/airports/distance?from=GKA&to=MAG <br> 5. Click Send |
| **Expected Result** | Status 200,  distance calculated |
| **Actual Result** | Status 200, Response includes distance in miles and kilometers| 
| **Status** | PASS |

### API-TC-05: GET /airports/distance — Missing parameters

| Field | Value |
|----|----|
|**Description** | Verify distance between two airports is calculated correctly |
| **Preconditions** | Open Postman|
| **Test Data**  |Method: POST, Url: 	https://airportgap.com/api/airports/distance?from=GKA |
|**Steps** | 1. Create a new request <br> 3. Set method to POST <br> 4. Enter URL: https://airportgap.com/api/airports/distance?from=GKA <br> 5. Click Send |
| **Expected Result** | Status 400 Bad Request <br> Error message explains missing parameter |
| **Actual Result** | Status 400, error: "to parameter is required | 
| **Status** | PASS |

### API-TC-06: POST /favorites — Add airport to favorites

| Field | Value |
|----|----|
|**Description** | 	Verify user can add an airport to favorites |
| **Preconditions** | Open Postman|
| **Test Data**  |Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "airport_id": "GKA" }|
|**Steps** | 1. Create a new request <br> 3. Set method to POST <br> 4. Enter URL: https://airportgap.com/api/favorites <br> 5.Enter Body:{ "airport_id": "GKA" } <br> 6. Click Send |
| **Expected Result** | 	Status 201, favorite created |
| **Actual Result** | Status 201 Created <br> Response includes the favorite airport| 
| **Status** | PASS |
