# Test Plan - Airport Gap API Testing

**Project:** Airport Gap API Testing
**Tester:** Zahid Solorzano
**Base URL:** https://airportgap.com/api
**Tool:** Postman

## Scope

### In Scope
- Authentication (Token)
- GET /airports — get all airports
- GET /airports/:id — get specific airport
- GET /airports/distance — calculate distance between two airports
- POST /favorites — save a favorite airport
- GET /favorites — list favorite airports
- DELETE /favorites/:id — remove a favorite
- Positive and negative test cases

### Out of Scope
- Performance/Load testing
- Security testing beyond authentication

## Endpoints to Test

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /airports | Get list of airports |
| GET | /airports/:id | Get specific airport |
| GET | /airports/distance | Calculate distance between airports |
| POST | /favorites | Add airport to favorites |
| GET | /favorites | Get my favorite airports |
| DELETE | /favorites/:id | Remove airport from favorites |

## Authentication
All requests require a token:


## Test Environment
| Component | Details |
|-----------|---------|
| API URL | https://jsonplaceholder.typicode.com |
| Tool | Postman v10+ |
| OS | Windows 10 |

## Test Deliverables
- Test cases
- Bug report (if applicable)
- Postman collection
- Screenshots
