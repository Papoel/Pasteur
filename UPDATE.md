# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Wish

- Sell Products like cake pie, or other products 

### Added
 - /

### Fixed

- Fix bug on page UserProfile format phone Twig.
- Fix missing filter Twig for format html tags in description event.
- Add Maintenance page for the website.


## v-1.1.1 - (30-01-2023)

### Added

- Redirect to homepage when clicking on the logo
- New navbar design
- Now the "Super Admin" can delete a messages from the user
- Configuration Monolog for intercept error in PROD + deprecation message
- Admin can delete registration
- Command for add new user from terminal

### Changed

- Page CGU :
  - Fixed typo in the paragraph "Hébergement du site"
  - Add design link in the paragraph "Hébergement du site"
  - Spelling mistakes correction
- Form [EventRegistrationFormType] :
  - Change the placeholder for field "firstname" and "lastname"
- User ask delete account :
  - Add missing parameter for redirect User
  - Complete the demarche for delete account
  - SUPER_ADMIN recieve an email when a user ask delete account and email is save in database
  - SUPER_ADMIN can delete user account and message
- Administration Panel
  - Admin can delete registration
  - TextEditorField for description, image is now unsortable
- Entity User
  - Change minimal characters require for complement address 5 to 4

### Removed

- Nothing yet

 ---

## v-1.0.0 - 28-01-2023
> The project is deployed.

---

# Bug report

Admin sur les cartes détails d'événements je ne vois pas le seul insrit pour l'événement
