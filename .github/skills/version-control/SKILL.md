---
name: version-control
description: Use when: setting up version control or following Git best practices in projects. Provides a quick checklist for Git setup and workflows.
---

# Version Control Skill

This skill guides you through setting up Git and following best practices for version control in the SiteCounter project.

## Quick Checklist

1. **Initialize Repository**
   - Run `git init` in the project root
   - Add remote origin if using GitHub: `git remote add origin <repository-url>`

2. **Configure .gitignore**
   - Create a .gitignore file in the root
   - Include common PHP/CodeIgniter ignores: vendor/, writable/logs/, .env, etc.

3. **Branching Strategy**
   - Use `main` as the default branch
   - Create feature branches for new work: `git checkout -b feature/user-auth`
   - Use release branches for preparing versions: `git checkout -b release/v1.0`

4. **Committing Changes**
   - Stage relevant files: `git add <files>` or `git add .` for all
   - Write clear, descriptive commit messages following conventional commits
   - Commit logical units of work: `git commit -m "feat: implement user login"`

5. **Pushing and Pulling**
   - Push local branches to remote: `git push origin feature/branch`
   - Pull latest changes: `git pull origin main`
   - Fetch remote changes without merging: `git fetch origin`

6. **Handling Conflicts**
   - Identify conflicting files during merge/pull
   - Edit files to resolve conflicts, keeping desired changes
   - Stage resolved files: `git add <resolved-file>`
   - Complete the merge: `git commit`

7. **Best Practices**
   - Avoid committing sensitive data (passwords, keys)
   - Use pull requests for code reviews and collaboration
   - Keep commit history clean; use interactive rebase for local cleanup
   - Tag releases: `git tag -a v1.0.0 -m "Release v1.0.0"`

This checklist promotes effective Git usage for collaborative development.