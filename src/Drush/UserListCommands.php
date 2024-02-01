<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Entity\EntityInterface;
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
    //#[CLI\Argument(name: 'arg1', description: 'Argument description.')]
        //#[CLI\Option(name: 'option-name', description: 'Option description')]
    #[CLI\Usage(name: 'user:list', description: 'List of the users with their user ID and username')]
    #[CLI\Usage(name: 'user:list --fields=uid', description: 'List of the user IDs')]
    #[CLI\Usage(name: 'user:list --fields=username', description: 'List of the usernames')]
    #[CLI\FieldLabels(labels:[
        'uid' => 'User ID',
        'username' => 'Username'
    ])]
    #[CLI\DefaultTableFields(fields: ['uid', 'username'])]
    public function userList(): RowsOfFields {
        $query = \Drupal::entityQuery('user');
        $query->accessCheck(false)->sort('uid', 'ASC');
        $arrayOfUids = $query->execute();
        $rows = [];
        foreach ($arrayOfUids as $uid) {
            $account = User::load($uid);
            if ($account !== null) {
                $rows[] = ['uid' => $uid, 'username' => $account->getDisplayName()];
            }
        }
        return new RowsOfFields($rows);
    }

}
