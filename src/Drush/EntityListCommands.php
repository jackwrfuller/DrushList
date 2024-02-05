<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\FormatterManager;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drush\Attributes as CLI;
use Symfony\Component\Console\Helper\Table;

final class EntityListCommands extends AbstractListCommands
{

    const MAX_COLUMNS = 8;
    private EntityFieldManager $entityFieldManager;
    private string $entityTypeID;
    private string  $entityTypeClass;
    private array $commandOptions;

    private array $rowsToDisplay;


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
    #[CLI\Option(name: 'nfields', description: 'The number of fields to display, default is 8.')]
    public function entityList(string $entityType = null, $options = ['nfields' => self::MAX_COLUMNS]): void
    {
        if ($entityType === null)
        {
            $this->renderEntityTypeList();
            return;
        }
        // Get required metadata for command
        $this->entityTypeID = \Drupal::entityTypeManager()->getStorage($entityType)->getEntityType()->getKey('id');
        $this->entityTypeClass = \Drupal::entityTypeManager()->getStorage($entityType)->getEntityClass();
        $this->commandOptions = $options;

        // Ensure the requested entity type is fieldable.
        if (!is_subclass_of($this->entityTypeClass, 'Drupal\Core\Entity\FieldableEntityInterface')) {
            throw new \Exception(dt("The specified entity type '!entity' is not a fieldable entity.", ['!entity' => $entityType]));
        }
        $this->renderEntityFields($entityType);
    }

    private function renderEntityTypeList(): void {
        $table = new Table($this->output());
        // Gets all fieldable entities, which are the only ones that work to list fields
        $entityTypes = array_keys(\Drupal::service('entity_field.manager')->getFieldMap());
        foreach ($entityTypes as $i => $type) {
            $table->addRow([$i => $type]);
        }
        $table->setHeaders(["Fieldable entities"]);
        $table->render();
    }

    private function renderEntityFields(string $entityType): void {
        $query = \Drupal::entityQuery($entityType);
        $query->accessCheck(false)->sort($this->entityTypeID);
        $arrayOfIds = $query->execute();
        if ($arrayOfIds === []) {
            $this->output->writeln("None were found!");
            return;
        }
        $this->rowsToDisplay = $this->createArrayOfRows($this->entityTypeClass , $arrayOfIds);
        $this->writeRowsToConsole($entityType);
    }
    private function writeRowsToConsole(string $entityType): void {
        $formatterManager = new FormatterManager();
        $labels = $this->getEntityFieldLabels($entityType);
        $numberOfCols = intval($this->commandOptions['nfields']);
        $wantedFieldLabels = array_slice($labels, 0, $numberOfCols, true);
        $opts = [
            FormatterOptions::FIELD_LABELS => $wantedFieldLabels,
        ];
        $formatterOptions = new FormatterOptions([], $opts);
        $formatterManager->write($this->output, 'table', new RowsOfFields($this->rowsToDisplay), $formatterOptions);
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