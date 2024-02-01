<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal;
use Drupal\node\Entity\Node;
use Drush\Attributes as CLI;
use Drush\Utils\StringUtils;

final class NodeListCommands extends AbstractListCommands
{

    /**
     * List all Drupal users.
     */
    #[CLI\Command(name: 'node:list', aliases: ['nl'])]
    #[CLI\Argument(name: 'nodeTypes', description: 'A comma delimited list of node types')]
    //#[CLI\Option(name: 'option-name', description: 'Option description')]
    #[CLI\Usage(name: 'node:list', description: 'List of nodes')]
//    #[CLI\Usage(name: 'node:list --fields=uid', description: 'List of the user IDs')]
//    #[CLI\Usage(name: 'node:list --fields=username', description: 'List of the usernames')]
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
        $query->accessCheck(false)->sort('nid', 'ASC');
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
            $node = Node::load($nid);
            if ($node !== null) {
                $rows[] = ['nid' => $nid, 'vid' => $node->id(), 'type' => $node->get('type')->getString(),
                    'uuid' => $node->get('uuid')->getString(), 'langcode' => $node->get('langcode')->getString()];
            }
        }
        return $rows;
    }


}