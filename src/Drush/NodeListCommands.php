<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal;
use Drupal\node\Entity\Node;
use Drush\Attributes as CLI;
use Drush\Utils\StringUtils;

final class NodeListCommands extends AbstractListCommands
{
    private const LIST = 'node:list';


    /**
     * List all nodes of one of the listed types. By default, show all nodes.
     */
    #[CLI\Command(name: self::LIST, aliases: ['nl', 'node-list'])]
    #[CLI\Argument(name: 'nodeTypes', description: 'A comma delimited list of node types')]
    #[CLI\Usage(name: 'drush node:list [nodeTypes]', description: 'List all nodes')]
    #[CLI\FieldLabels(labels: [
        'nid' => 'Node ID',
        'vid' => 'Version ID',
        'type' => 'Node type',
        'uuid' => 'Unique user ID',
        'langcode' => 'Language'
    ])]
    #[CLI\DefaultTableFields(fields: ['nid', 'vid', 'type', 'uuid', 'langcode'])]
    public function nodeList(string $nodeTypes = null): RowsOfFields
    {
        $nodeTypeArray = StringUtils::csvToArray($nodeTypes);
        $arrayOfNodes = $this->getAllNodesOfTypes($nodeTypeArray);
        $rows = $this->createPrintableMatrix($arrayOfNodes);
        return new RowsOfFields($rows);
    }

    private function getAllNodesOfTypes(array $nodeTypeArray): array {
        $query = Drupal::entityQuery('node');
        $query->accessCheck(false)->sort('nid');
        // Skip or-conditioning if no arguments provided.
        if ($nodeTypeArray === []) {
            return $query->execute();
        }
        // Show only selected node types.
        $orGroup = $query->orConditionGroup();
        foreach ($nodeTypeArray as $type) {
            assert(is_string($type));
            $orGroup->condition('type', $type);
        }
        $query->condition($orGroup);
        return $query->execute();
    }

    private function createPrintableMatrix(array $arrayOfNodes): array {
        $rows = [];
        foreach ($arrayOfNodes as $nid) {
            $this->addRow($rows, $nid);
        }
        return $rows;
    }

    private function addRow(array& $rows, $nodeId): void {
        $node = Node::load($nodeId);
        if ($node === null) {
            $this->logger()->warning("Unable to load node {$nodeId}");
            return;
        }
        $keys = array_keys($node->toArray());
        $row = [];
        foreach ($keys as $key) {
            $row[$key] = $node->get($key)->getString();
        }
        $rows[] = $row;
    }



}