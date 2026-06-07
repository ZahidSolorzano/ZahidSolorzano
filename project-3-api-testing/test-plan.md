# Test Plan - Airport Gap API Testing

**Project:** Airport Gap API Testing <br>
**Tester:** Zahid Solorzano <br>
**Base URL:** https://airportgap.com/api <br>
**Tool:** Postman

## Introduction
This testing is part of my portfolio as an aspiring QA Engineer, demonstrating my ability to design and execute API test cases, report bugs, and document findings in a professional manner.

## Objective
Ensure that all endpoints work as expected under normal conditions.Confirm that protected endpoints reject unauthorized requests and accept valid tokens. Validate that the API returns appropriate HTTP status codes and clear error messages for invalid inputs or missing parameters. Find and document any ambiguous error messages or unexpected behaviors

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
- Performance or Load testing
- Security testing beyond authentication
- API Documentation

## Team
Since this is a personal project meant to show my software testing skills, the only person involved is Zahid Solorzano.

## Test Strategy

| Test Level | Test Types | Test Techniques | Manual or Automated |
|------------|------------|-----------------|---------------------|
| System testing, Integration testing | Functional, Negative, Authentication, Error handling | Equivalence Partitioning, Boundary Value Analysis, Positive/Negative testing, Schema validation | Manual (Postman) |

# Criteria
The test execution will start as soon as all the test cases are designed and reviewed and the test environment is ready.
The test will be considered completed when all planned test cases have been executed, all found defects has been documented, the evidence has been captured and this test plan has been completed

## Endpoints to Test

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /airports | Get list of airports |
| GET | /airports/:id | Get specific airport |
| POST | /airports/distance | Calculate distance between airports |
| POST | /favorites | Add airport to favorites |
| GET | /favorites | Get my favorite airports |
| DELETE | /favorites/:id | Remove airport from favorites |

## Authentication
Some requests require a token: 32MAXs3SX4oHVmWc5L74fbcG

## Test Environment
| Component | Details |
|-----------|---------|
| API URL | https://airportgap.com/api |
| Tool | Postman v12.13.6  |
| OS | Windows 10 |

## Test Deliverables
- Test cases
- Bug report
- Postman collection
- Screenshots

## Risks and Mitigation
| Risks | Likelihood | Mitigation |
|------|------|----|
|Failure to complete test design and execution within the established timeframe| Low | Conduct a daily follow of activities done and pending to do|

## Timeline
| Day | Tasks |
|-----|-------|
| Day 1 | Task 1: Test Plan |
| Day 2 to 4 | Task 2: Test Cases design |
| Day 5 | Task 3 + 4: Environment setup + Execution |
| Day 6 | Task 5 + 6: Bug reporting + Evidence |
| Day 7 | Task 7: Final review and upload |
