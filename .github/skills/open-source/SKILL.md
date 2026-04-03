---
name: open-source
description: Use when: preparing a CodeIgniter project for open source release. Provides a quick checklist for licensing, documentation, and code commenting.
---

# Open Source Skill

This skill guides you through preparing a CodeIgniter project for open source release, including documentation and code comments.

## Quick Checklist

1. **Choose a License**
   - Select an appropriate open source license (e.g., MIT for permissive, GPL for copyleft)
   - Create a LICENSE file in the project root with the full license text

2. **Create Documentation**
   - Write a detailed README.md including:
     - Project description and features
     - Installation and setup instructions
     - Usage examples
     - Contributing guidelines
   - Add inline documentation for APIs if applicable

3. **Add Code Comments**
   - Add PHPDoc comments to all classes, methods, and properties
   - Comment complex logic and algorithms
   - Ensure comments explain the "why" not just the "what"

4. **Set Up Repository**
   - Create a public GitHub repository
   - Initialize Git and push the codebase
   - Configure .gitignore to exclude sensitive files
   - Add issue and pull request templates

5. **Version Tagging**
   - Follow semantic versioning (MAJOR.MINOR.PATCH)
   - Create annotated tags for releases: `git tag -a v1.0.0 -m "Initial release"`
   - Push tags to GitHub: `git push --tags`
   - Use GitHub releases for each tag with changelog

6. **Community Engagement**
   - Create CONTRIBUTING.md with development setup and contribution process
   - Set up GitHub Actions for CI/CD (testing, linting)
   - Announce the project release on CodeIgniter forums or relevant communities

7. **Security and Maintenance**
   - Remove any hardcoded secrets or sensitive data
   - Set up automated dependency updates (e.g., Dependabot)
   - Enable security vulnerability scanning

This checklist prepares the SiteCounter project for successful open source adoption.