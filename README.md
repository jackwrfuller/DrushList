# Drush User List

### A Simple extension of Drush

This module augments Drush's command set to include the ability to list various entities.

A task that I found was not as simple as it should be in Drush was listing entities, i.e
list all users, list all nodes, list all webforms, etc. 

While this can be done with SQL queries, Drush provided similar functionality for other commands.
For example, `drush role:list` or `drush views:list`. 

## :book: Contents

- [Requirements](#hammer-requirements)
- [Installation](#building_construction-installation)
- [Usage](#thought_balloon-usage)
- [About](#cook-author)
- [License](#page_with_curl-license)

## :hammer: Requirements

- PHP >=8.0.0
- Drush >=12.0.0
- Drupal >= 10.0.0

## :building_construction: Installation

```bash
composer require jackwrfuller/drush-list
```

Since this package is considered a Drupal module, you need to enable the module as well:

```bash
drush pm:install drush_list
```

## :thought_balloon: Usage

```bash
drush entity:list
```

Returns a list of all fieldable object types

```bash
drush entity:list webform
```

Returns a list of all webforms. 


```bash
drush user:list
```
Returns a table with user IDs and usernames for all users in the database. 

Optionally, you can filter the columns using `--field=<column>`, i.e:

```bash
drush user:list --field=uid
```

to get just a list of user IDs.

```bash
drush node:list [...node_type]
```

Display a list of all nodes, optionally filtering by node types.

---
## :page_with_curl: License

**Drush User List** is distributed under [MIT](https://opensource.org/licenses/MIT) license 🚀 Enjoy! ❤️
