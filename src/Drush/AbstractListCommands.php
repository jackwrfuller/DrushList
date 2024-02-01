<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Drupal\Core\Utility\Token;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AbstractListCommands extends DrushCommands
{
    /**
     * Constructs an UserListCommands object.
     */
    public function __construct(private readonly Token $token) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static($container->get('token'));
    }


}