# Test Plan - JSONPlaceholder API Testing

## Project Overview
- **Application:** JSONPlaceholder (fake REST API)
- **Base URL:** https://jsonplaceholder.typicode.com
- **Tester:** Zahid Solorzano
- **Tool:** Postman

## Scope
### In Scope
- Test /posts endpoint
- Methods: GET, POST, PUT, PATCH, DELETE
- Validate status codes and response structure
- Positive and negative test cases

### Out of Scope
- Authentication (not required)
- Performance/Load testing
- Other endpoints (/users, /comments)

## Endpoints to Test

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /posts | Get all posts |
| GET | /posts/1 | Get a specific post |
| POST | /posts | Create a new post |
| PUT | /posts/1 | Update a post (full) |
| PATCH | /posts/1 | Update a post (partial) |
| DELETE | /posts/1 | Delete a post |

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
