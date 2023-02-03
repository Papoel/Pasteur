CHANGELOG
=========

1.1.3
---
03-02-2023

### Added

### Changed

* Fix bug on after registration if user has not phone number 500 is generated.
* Fix depreciation: symfony 6.2 we must use render instead of renderForm.

---

1.1.2
---
02-02-2023

### Added

* New template for payment after registration.
* Add alert message for prevent user of the site is soon in maintenance.

### Changed

* Rename folder User in user 

---

1.1.1
---

30-01-2023

### Added

* Redirect to homepage when clicking on the logo.
* New navbar design.
* "Super Admin" can delete a messages from the user in admin panel.
* Configuration Monolog for intercept error in PROD + deprecation message.
* Admin can delete registration.
* Command for add new user from terminal.
* Fix bug on page UserProfile format phone Twig.
* Fix missing filter Twig for format html tags in description event.
* Add Maintenance page for the website.

### Changed

###### Entity User
    - Change minimal characters require for complement address 5 to 4

###### Administration Panel
    - Admin can delete registration
    - TextEditorField for description, image is now unsortable

###### User ask delete account :
    - Add missing parameter for redirect User
    - Complete the demarche for delete account
    - SUPER_ADMIN recieve an email when a user ask delete account and email is save in database
    - SUPER_ADMIN can delete user account and message

###### Form [EventRegistrationFormType] :
    - Change the placeholder for field "firstname" and "lastname"

###### Page CGU :
    - Fixed typo in the paragraph "Hébergement du site"
    - Add design link in the paragraph "Hébergement du site"
    - Spelling mistakes correction

---

1.0.0 
---

28-01-2023
> The project is deployed.
