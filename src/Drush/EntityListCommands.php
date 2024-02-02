<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\FormatterManager;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drush\Attributes as CLI;



final class EntityListCommands extends AbstractListCommands
{

    private EntityFieldManager $entityFieldManager;
    public function __construct(EntityFieldManager $entityFieldManager) {
        $this->entityFieldManager = $entityFieldManager;
        parent::__construct();
    }

    public static function create(ContainerInterface $container): self
    {
        return new static($container->get('entity_field.manager'));
    }

    #[CLI\Command(name: 'entity:list')]
    #[CLI\Argument(name: 'entityType', description: 'entity type to display')]
    public function entityList(string $entityType): void
    {
        // Get required metadata about the requested entity type
        $entityTypeClass = \Drupal::entityTypeManager()->getStorage($entityType)->getEntityClass();
        $entityTypeID = \Drupal::entityTypeManager()->getStorage($entityType)->getEntityType()->getKey('id');


        $query = \Drupal::entityQuery($entityType);
        $query->accessCheck(false)->sort($entityTypeID);
        $arrayOfIds = $query->execute();

        $rows = $this->createPrintableMatrix($entityTypeClass , $arrayOfIds);

        $formatterManager = new FormatterManager();
        $opts = [
            FormatterOptions::FIELD_LABELS => $this->getEntityFieldLabels($entityType),
        ];
        $formatterOptions = new FormatterOptions([], $opts);

        $formatterManager->write($this->output, 'table', new RowsOfFields($rows), $formatterOptions);
    }

    private function getEntityFieldLabels(string $entityType): array
    {
        $fieldNameAndLabels = [];
        $fields = $this->entityFieldManager->getBaseFieldDefinitions($entityType);
        foreach ($fields as $fieldName => $fieldDefinition) {
            $fieldNameAndLabels[$fieldName] = $fieldDefinition->getLabel();
        }
        return $fieldNameAndLabels;
    }
}