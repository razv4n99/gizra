<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

/**
 * Helper for a grid of Person Cards.
 */
trait PersonCardGridThemeTrait {

  /**
   * Build a grid of Person cards.
   *
   * @param array $items
   *   Render arrays of Person cards.
   */
  protected function buildElementPersonCardGrid(array $items): array {
    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'grid',
          'grid-cols-1',
          'sm:grid-cols-2',
          'md:grid-cols-3',
          'lg:grid-cols-3',
          'gap-6',
        ],
      ],
      'items' => $items,
    ];
  }

}
