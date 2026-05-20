# Test Plan - Login Functionality

**Project:** SauceDemo Login Testing  
**Tester:** Zahid Solorzano  
**Date:** 05/18/2026

## Introduction

The purpose of this document is to provide the information and framework required to plan and develop the testing process activities for the login functionality of the demo e-commerce site SauceDemo (https://www.saucedemo.com).
This project is part of my portfolio as an aspiring Junior Tester, with the goal of demonstrating my manual testing skills to potential employers like BairesDev.

---
## Objective
Validate that the login functionality of SauceDemo works correctly for different user types, handles invalid inputs gracefully, and provides clear error messages.

##  Scope

### In Scope 
- Login with valid credentials (standard_user, problem_user, performance_glitch_user)
- Login with locked_out_user (negative test)
- Login with empty username/password
- Login with invalid username/password
- Error messages for each failure scenario
- Page redirect after successful login

### Out of Scope 
- Password recovery / "Forgot password" 
- Remember me functionality 
- Login via social media 
- Performance / load testing 
- Security testing

## Team
Since this test plan is part of a personal project aimed at demonstrating software testing skills, the only person involved in its entire development is Zahid Solorzano

## Test Strategy

The following table describes the testing approach for this project:

| Test Level | Test Types | Test Techniques | Manual or Automated |
|------------|------------|----------------|---------------------|
| System Testing, UI Testing | Functional, Negative, Usability | Black box, Equivalence partitioning, Boundary value analysis | Manual |


## Criteria

### Start Criteria
Test execution will begin as soon as:
- Test cases have been designed and reviewed
- The test environment is ready (browser, internet connection)

### Exit Criteria
The testing process will be considered COMPLETE when:
- All planned test cases have been executed (8/8)
- All found defects have been documented (2/2 reported)
- Test evidence (screenshots) has been captured
- This test plan has been completed

## Test Environment

| Component | Details |
|-----------|---------|
| **Application URL** | https://www.saucedemo.com |
| **Browser** | Google Chrome ( |
| **Operating System** | Windows10 |
| **Network** | Home Wi-Fi  |
| **Device** |  Desktop |
| **Testing Tools** | GitHub , Markdown, Screenshot tool |

---

## Test Deliverables 

| Document | Status | Location |
|----------|--------|----------|
| Test Plan | Completed | `test-plan.md` |
| Test Cases | Completed (8 cases) | `test-cases.md` |
| Bug Report | Completed (2 bugs) | `bug-report.md` |
| Test Evidence  | Completed | `evidence/` folder |

## Risks & Mitigation

| Risk | Likelihood | Mitigation |
|------|-----------|------------|
| Demo application changes without notice | Low | Document the date of testing; note if issues appear |
| Network issues affecting response time | Medium | Note network conditions in bug report |
| Browser-specific issues | Low | Tested only on Chrome; mention in scope |

## Timeline
| Day | Tasks |
|-----|-------|
| Day 1 | Task 1: Test Plan |
| Day 2 | Task 2: Test Cases design |
| Day 3 | Task 3 + 4: Environment setup + Execution |
| Day 4 | Task 5 + 6: Bug reporting + Evidence |
| Day 6 | Task 7: Final review and upload |





