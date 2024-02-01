<?php declare(strict_types=1);

namespace Drupal\drush_user_list\Drush;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drush\Attributes as CLI;

class NodeListCommands extends AbstractListCommands
{

    /**
     * List all Drupal users.
     */
    #[CLI\Command(name: 'node:list', aliases: ['nl'])]
    //#[CLI\Argument(name: 'arg1', description: 'Argument description.')]
        //#[CLI\Option(name: 'option-name', description: 'Option description')]
    #[CLI\Usage(name: 'node:list', description: 'List of nodes')]
//    #[CLI\Usage(name: 'node:list --fields=uid', description: 'List of the user IDs')]
//    #[CLI\Usage(name: 'node:list --fields=username', description: 'List of the usernames')]
    #[CLI\FieldLabels(labels:[
        'nid' => 'Node ID',
        'vid' => 'Version ID',
        'type' => 'Node type',
        'uuid' => 'Unique user ID',
        'langcode' => 'Language'
    ])]
    #[CLI\DefaultTableFields(fields: ['nid', 'vid', 'type', 'uuid', 'langcode'])]
    public function nodeList(): RowsOfFields {
        $query = \Drupal::entityQuery('node');
        $query->accessCheck(false)->sort('nid', 'ASC');
        //$query->condition('type', 'webform');
        $arrayOfNodes = $query->execute();
        $rows = [];
        foreach ($arrayOfNodes as $nid) {
            $node = Node::load($nid);
            if ($node !== null) {
                $rows[] = ['nid' => $nid, 'vid' => $node->id(), 'type' => $node->get('type')->getString(),
                    'uuid' => $node->get('uuid')->getString(), 'langcode' => $node->get('langcode')->getString()];
            }
        }
        return new RowsOfFields($rows);
    }
}