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
