# Drush User List

### A Simple extension of the 'drush user' command

Extends the capability of Drush's 'user' command to include the ability to list all users, in the same way you can list all views or roles with 'drush views:list' and 'drush roles:list', respecitively.

## :book: Contents

- [Requirements](#hammer-requirements)
- [Installation](#building_construction-installation)
- [Usage](#thought_balloon-usage)
- [About](#cook-author)
- [License](#page_with_curl-license)

## :hammer: Requirements

- PHP >=8.0.0
- Drush >=12.0.0

## :building_construction: Installation

```bash
composer require jackwrfuller/drush-user-list
```

Since this package is considered a Drupal module, you may need to enable the module as well:

```bash
drush pm:install drush_user_list
```

## :thought_balloon: Usage

```bash
drush user:list
```
Returns a table with user IDs and usernames for all users in the database. 

Optionally, you can filter the columns using `--field=<column>`, i.e:

```bash
drush user:list --field=uid
```

to get just a list of user IDs.

---
## :page_with_curl: License

**Drush User List** is distributed under [MIT](https://opensource.org/licenses/MIT) license 🚀 Enjoy! ❤️
