<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\user\Entity\User;
use Drush\Attributes as CLI;

/**
 * Adds a command to list all users in a Drupal site.
 *
 * Jack WR Fuller
 */
final class UserListCommands extends AbstractListCommands {

    /**
     * List all Drupal users.
     */
    #[CLI\Command(name: 'user:list', aliases: ['ul'])]
    #[CLI\Usage(name: 'drush user:list', description: 'List of the users with their user ID and username')]
    #[CLI\FieldLabels(labels:[
        'uid' => 'User ID',
        'name' => 'Username',
        'mail' => 'Email'
    ])]
    #[CLI\DefaultTableFields(fields: ['uid', 'name', 'mail'])]
    public function userList(): RowsOfFields
    {
        $arrayOfIds = $this->getUserIds();
        $rowsOfUsers = $this->createPrintableMatrix(User::class, $arrayOfIds);
        return new RowsOfFields($rowsOfUsers);
    }

    private function getUserIds(): array
    {
        $query = \Drupal::entityQuery('user');
        $query->accessCheck(false)->sort('uid', 'ASC');
        return $query->execute();
    }

}
