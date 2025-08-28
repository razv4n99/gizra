<?php
namespace Drupal\server_general\Plugin\EntityViewBuilder;

use Drupal\node\NodeInterface;
use Drupal\server_general\EntityViewBuilder\NodeViewBuilderAbstract;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\og\Og;
use Drupal\og\OgAccessInterface;

/**
 * The "Node Group" plugin.
 *
 * @EntityViewBuilder(
 * id = "node.group",
 * label = @Translation("Node - Group"),
 * description = "Node view builder for Group bundle."
 * )
 */
class NodeGroup extends NodeViewBuilderAbstract {

  /**
   * The OG access checker.
   *
   * @var \Drupal\og\OgAccessInterface
   */
  protected $ogAccess;

  /**
   * {@inheritdoc}
   */
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->ogAccess = $container->get('og.access');
    return $instance;
  }

  /**
   * Build full view mode.
   *
   * @param array $build
   * The render array.
   * @param \Drupal\node\NodeInterface $entity
   * The entity.
   *
   * @return array
   * The render array.
   */
  public function buildFull(array $build, NodeInterface $entity) {
    /** @var \Drupal\Core\Session\AccountInterface $current_user */
    $current_user = \Drupal::currentUser();

    // Check if the user is authenticated, otherwise there's nothing to do.
    if (!$current_user->isAuthenticated()) {
      return $build;
    }

    // Check if the current node is an organic group.
    $is_og_group = Og::isGroup('node', $entity->bundle());

    // Only proceed if it's an OG group.
    if (!$is_og_group) {
      return $build;
    }

    // Check if the current user is already a member of this group.
    $is_member = Og::isMember($entity, $current_user);

    // If the user is not a member, we can check if they are allowed to subscribe.
    if (!$is_member) {
      // Use the og.access service to check for the 'subscribe' permission.
      // The arguments are reordered to match the older OG API.
      $can_subscribe = $this->ogAccess->userAccess($entity, 'subscribe', $current_user);

      // Only show the greeting if the user has permission to subscribe.
      if ($can_subscribe instanceof AccessResult && $can_subscribe->isAllowed()) {
        $group_label = $entity->label();
        $name = $current_user->getDisplayName();

        // Get the URL for subscribing to the group.
        $subscribe_url = \Drupal\Core\Url::fromRoute('og.subscribe', [
          'entity_type_id' => 'node',
          'group' => $entity->id(),
        ])->toString();

        // Add the greeting render array to the build.
        $build['subscribe_greeting'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['og-subscribe-greeting', 'mb-4']],
          'content' => [
            '#markup' => $this->t(
              'Hi @name, <a href=":url">click here</a> if you would like to subscribe to this group called <strong>@label</strong>.',
              [
                '@name' => $name,
                ':url' => $subscribe_url,
                '@label' => $group_label,
              ]
            ),
          ],
        ];
      }
    }

    // Add the default content.
    $build['group_content'] = [
      '#markup' => $this->t('Group content goes here.'),
    ];

    return $build;
  }
}
