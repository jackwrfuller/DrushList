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

    const LABELS = [
        'uid' => 'User ID',
        'name' => 'User name',
        'pass' => 'Password',
        'mail' => 'User mail',
        'theme' => 'User theme',
        'signature' => 'Signature',
        'signature_format' => 'Signature format',
        'user_created' => 'User created',
        'created' => 'Created',
        'user_access' => 'User last access',
        'access' => 'Last access',
        'user_login' => 'User last login',
        'login' => 'Last login',
        'user_status' => 'User status',
        'status' => 'Status',
        'timezone' => 'Time zone',
        'picture' => 'User picture',
        'init' => 'Initial user mail',
        'roles' => 'User roles',
        'group_audience' => 'Group Audience',
        'langcode' => 'Language code',
        'uuid' => 'Uuid',
    ];
    const DEFAULT_FIELDS = ['uid', 'name', 'mail', 'roles', 'user_status'];

    /**
     * List all Drupal users.
     */
    #[CLI\Command(name: 'user:list', aliases: ['ul'])]
    #[CLI\Usage(name: 'drush user:list', description: 'List of the users with their user ID and username')]
    #[CLI\FieldLabels(labels: self::LABELS)]
    #[CLI\DefaultTableFields(fields: self::DEFAULT_FIELDS)]
    public function userList(): RowsOfFields
    {
        $arrayOfIds = $this->getUserIds();
        $rowsOfUsers = $this->createPrintableMatrix(User::class, $arrayOfIds);
        return new RowsOfFields($rowsOfUsers);
    }

    private function getUserIds(): array
    {
        $query = \Drupal::entityQuery('user');
        $query->accessCheck(false)->sort('uid');
        return $query->execute();
    }

}
