---
name: testing
description: Use when: implementing testing in CodeIgniter projects. Provides a quick checklist for best practices and acceptance testing at each stage.
---

# Testing Skill

This skill guides you through best practices for testing in CodeIgniter, ensuring acceptance testing at each implementation stage.

## Quick Checklist

1. **Set Up Testing Environment**
   - Install PHPUnit via Composer: `composer require --dev phpunit/phpunit`
   - Configure `phpunit.xml` in the project root for test suites

2. **Unit Testing**
   - Create tests for models in `tests/unit/`
   - Test business logic, data validation, and model methods

3. **Integration Testing**
   - Write tests for controllers and routes in `tests/integration/`
   - Use CodeIgniter's `TestResponse` for HTTP request testing

4. **Acceptance Testing**
   - Perform manual acceptance testing for each feature
   - Test complete user workflows: user login, adding websites, tracking page loads
   - Document test scenarios and results

5. **Run Tests at Each Stage**
   - Execute tests after implementing each feature: `vendor/bin/phpunit`
   - Ensure all tests pass before merging code
   - Integrate with CI/CD pipelines for automated testing

6. **Best Practices**
   - Aim for high test coverage (>80%)
   - Test edge cases, error handling, and security scenarios
   - Mock external services and databases where appropriate
   - Follow Test-Driven Development (TDD) principles

This checklist is tailored for CodeIgniter 4's built-in testing support with PHPUnit.