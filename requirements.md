# Project objective

To create an easy to install web application that allows an administrator to track usage of one or more web sites.

# Installation

- The application should allow a simple installation process with no need to use the command line
- The installation should use SQLite only for the initial public release
- The installation script should not be able to be run once the application is installed

# Frameworks

- The application should be based on CodeIgniter 4
- The front end should use Bootstrap 5
- Icons should use the Boostrap library

# Language

- The initial language should be English
- The application should allow additional language packs to be created

# Security

- Access to the application should be through a username and password combination
- Passwords should be a minimum of 8 characters

# Users

- The application will initially have one user, the administrator
- The user will be represented by a firstname and lastname and an email address
- The user should be able to change their password securely from within their profile
- The user should be able to request a new password from the login screen
- The user should be able to change the following settings:
    - Preferred language

# Website setup

- The user will be able to add, edit and delete a website entry
- Each website entry will contain the name of the site and its URL
- Once added, the entry should provide a javascript tracking script for addition to the tracked website
- The user should be able to copy the tracking script to the clipboard via a single button

# Tracking

- The tracking script should call the application each time a page is loaded on the tracked site
- The application should track the following data:
    - Time and date of the page load
    - Page title
    - Page URL
    - Visitor ID
- THe visitor ID will be a unique identifier for the user
- The visitor ID will be set once by the browser and held in a single cookie
- Return visits will use the data set in the cookie

# Reports

- For each website being tracked, the user should be able to view the following datasets:
    - Average number of unique visitors per day
    - Average number of unique visitors per month
    - Average number of unique visitors per year
    - Total number of unique visitors to date
    - A table showing the most popular 10 pages by page views
    - A table showing the least popular 10 pages by page views
    - A chart showing a timeline on the x-axis with a bar on the y-axis to indicate the total number of page views per day

# CORS

- The application should handle CORS security requirements