# Bug Report - Airport Gap Functionality

### API-BUG-01: Confusing error message when using wrong field name

| Field | Value |
|-------|-------|
| **Test Case** | API-TC-08: Favorites - Wrong field name |
| **Description** | When sending a request with an incorrect field name, the API returns an error message that suggests the airport code is invalid, rather than indicating that the required field is missing  |
| **Preconditions** |Open Postman<br> Open collection "Airports" |
| **Test Data** | Method: POST <br> Url: https://airportgap.com/api/favorites <br> Body: { "wrong_field": "GKA" } <br> Authentication Token "32MAXs3SX4oHVmWc5L74fbcG" |
| **Steps** | 1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/favorites <br> 4. In Authentication tab, select Bearer token type and enter "32MAXs3SX4oHVmWc5L74fbcG" <br> 5. In body tab select raw, select JSON an enter: { "wrong_field": "GKA" } <br> 6. Click Send  |
| **Expected Result** | Error message should indicate: "Required field airport_id is missing" |
| **Actual Result** | Error message: "Airport Please enter a valid airport code" |
| **Environment** | Windows 10, Postman v12.13.6  |
| **Severity** | Low |
| **Priority** | Low |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |

### API-BUG-02: Ambiguous error message for distance endpoint (Missing "to" parameter)

| Field | Value |
|-------|-------|
| **Test Case** | API-TC-14: Distance - Missing "to" parameter |
| **Description** | When one of the airport codes is missing, the API returns an error message that mentions both 'from' and 'to' airports, without specifying which one is missing |
| **Preconditions** |Open Postman<br> Open collection "Airports" |
| **Test Data** |Method: POST <br>  Url: 	https://airportgap.com/api/airports/distance?from=GKA |
| **Steps** |  1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA <br> 4. Click Send  |
| **Expected Result** | Error message should specify which parameter is missing (Missing airport code for "to" parameter)  |
| **Actual Result** | Generic message: "Please enter valid 'from' and 'to' airports"  |
| **Environment** |Windows 10, Postman v12.13.6   |
| **Severity** | Low |
| **Priority** | Medium |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |

### API-BUG-03: Ambiguous error message for distance endpoint (Missing "from" parameter)

| Field | Value |
|-------|-------|
| **Test Case** | API-TC-15:  Distance - Missing "from" parameter |
| **Description** | When one of the airport codes is missing, the API returns an error message that mentions both 'from' and 'to' airports, without specifying which one is missing |
| **Preconditions** |Open Postman<br> Open collection "Airports" |
| **Test Data** |Method: POST <br>  Url: 	https://airportgap.com/api/airports/distance?to=GKA |
| **Steps** |  1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?to=GKA <br> 4. Click Send  |
| **Expected Result** | Error message should specify which parameter is missing (Missing airport code for "from" parameter)  |
| **Actual Result** | Error message: "Airport Please enter a valid airport code" |
| **Environment** |Windows 10, Postman v12.13.6  |
| **Severity** | Low |
| **Priority** | Medium |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |

### API-BUG-04: Ambiguous error message for distance endpoint (Invalid "to" parameter)

| Field | Value |
|-------|-------|
| **Test Case** | API-TC-17: Distance with invalid airport code (in "to" parameter) |
| **Description** | When one of the airport codes is invalid, the API returns an error message that mentions both 'from' and 'to' airports, without specifying which one is invalid |
| **Preconditions** |Open Postman<br> Open collection "Airports" |
| **Test Data** |Method: POST <br>  Url: 	https://airportgap.com/api/airports/distance?from=GKA&to=999 |
| **Steps** |  1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=GKA&to=999 <br> 4. Click Send  |
| **Expected Result** | Error message should specify which parameter is invalid (Invalid airport code for "to" parameter)  |
| **Actual Result** | Generic message: "Please enter valid 'from' and 'to' airports"  |
| **Environment** |Windows 10, Postman v12.13.6   |
| **Severity** | Low |
| **Priority** | Medium |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |

### API-TC-18: Distance with invalid airport code (in "from" parameter)

| Field | Value |
|-------|-------|
| **Test Case** | API-TC-18: Distance with invalid airport code (in "from" parameter) |
| **Description** | When one of the airport codes is invalid, the API returns an error message that mentions both 'from' and 'to' airports, without specifying which one is invalid |
| **Preconditions** |Open Postman<br> Open collection "Airports" |
| **Test Data** |Method: POST <br>  Url: 	https://airportgap.com/api/airports/distance?from=999&to=GKA |
| **Steps** |  1. Create a new request <br> 2. Set method to POST <br> 3. Enter URL: https://airportgap.com/api/airports/distance?from=999&to=GKA <br> 4. Click Send  |
| **Expected Result** | Error message should specify which parameter is invalid (Invalid airport code for "from" parameter)  |
| **Actual Result** | Error message: "Airport Please enter a valid airport code" |
| **Environment** |Windows 10, Postman v12.13.6  |
| **Severity** | Low |
| **Priority** | Medium |
| **Status** | Open |
| **Reported By** | Zahid Solorzano |
