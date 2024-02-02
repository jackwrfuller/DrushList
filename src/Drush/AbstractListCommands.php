<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Utility\Token;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractListCommands extends DrushCommands
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

    protected function createPrintableMatrix(string $entityType, array $arrayOfIds): array
    {
        $rows = [];
        foreach ($arrayOfIds as $id) {
            // TODO refactor - it seems like a code smell, since we cant enforce that the entity type string will refer to a class that implement EntityInterface, which is where load() is defined.
            $entity = $entityType::load($id);
            if ($entity === null) {
                $this->logger()->warning("Unable to load entity {$id}");
                continue;
            }
            $this->addRow($rows, $entity);
        }
        return $rows;
    }

    private function addRow(array& $rows, FieldableEntityInterface $entity): void
    {
        $keys = array_keys($entity->toArray());
        $row = [];
        foreach ($keys as $key) {
            $row[$key] = $entity->get($key)->getString();
        }
        $rows[] = $row;
    }


}