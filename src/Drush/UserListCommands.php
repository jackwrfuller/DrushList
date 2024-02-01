<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;

/**
 * Adds a command to list all users in a Drupal site.
 *
 * Jack WR Fuller
 */
final class UserListCommands extends DrushCommands {

    /**
     * Constructs an UserListCommands object.
     */
    public function __construct(
        private readonly Token $token,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static($container->get('token'));
    }

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
    public function listAllUsers(): RowsOfFields {
        $query = \Drupal::entityQuery('user');
        $query->accessCheck(False)->sort('uid', 'ASC');
        $arrayOfUids = $query->execute();
        foreach ($arrayOfUids as $uid) {
            $account = User::load($uid);
            $rows[] = ['uid' => $uid,
                'username' => $account->getDisplayName()];

        }
        return new RowsOfFields($rows);
    }

}
