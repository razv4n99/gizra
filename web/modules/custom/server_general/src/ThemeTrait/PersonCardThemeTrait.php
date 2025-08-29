<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

use Drupal\server_general\ThemeTrait\Enum\AlignmentEnum;
use Drupal\server_general\ThemeTrait\Enum\FontSizeEnum;
use Drupal\server_general\ThemeTrait\Enum\FontWeightEnum;
use Drupal\server_general\ThemeTrait\Enum\TextColorEnum;
use Drupal\server_general\ThemeTrait\Enum\BackgroundColorEnum;

/**
 * Helper for a single Person Card.
 */
trait PersonCardThemeTrait {
  use ElementWrapThemeTrait;
  use InnerElementLayoutThemeTrait;

  /**
   * Wraps a render array with a background color.
   */
  protected function wrapBackgroundColor(array $element, BackgroundColorEnum $color): array {
    $class = match($color) {
      BackgroundColorEnum::LightGray => 'bg-gray-200',
      BackgroundColorEnum::Transparent => 'bg-transparent',
      BackgroundColorEnum::LightGreen => 'bg-green-100', // Tailwind light green
    };

    // Wrap the element in a div with background and rounded corners by default
    return [
      '#type' => 'container',
      '#attributes' => ['class' => [$class, 'rounded-lg', 'px-2', 'py-1', 'inline-block']],
      'content' => $element,
    ];
  }

  /**
   * Build a single Person card.
   */
  protected function buildElementPersonCard(
    string $image_url,
    string $alt,
    string $name,
    string $detail,
    string $badge
  ): array {
    $elements = [];

    // Image.
    $elements[] = $this->wrapRoundedCornersFull([
      '#theme' => 'image',
      '#uri' => $image_url,
      '#alt' => $alt,
      '#width' => 128,
    ]);

    $inner = [];

    // Name (bold).
    $name_el = $this->wrapTextFontWeight($name, FontWeightEnum::Bold);
    $inner[] = $this->wrapTextCenter($name_el);

    // Detail (sm, gray).
    $detail_el = $this->wrapTextResponsiveFontSize($detail, FontSizeEnum::Sm);
    $detail_el = $this->wrapTextCenter($detail_el);
    $inner[] = $this->wrapTextColor($detail_el, TextColorEnum::Gray);

    // Badge (xs)
    $badge_el = $this->wrapTextResponsiveFontSize($badge, FontSizeEnum::Xs);
    $badge_el = $this->wrapTextCenter($badge_el);
    $badge_el = $this->wrapTextColor($badge_el, TextColorEnum::DarkGreen);

    // Wrap with LightGreen background using enum
    $badge_el = $this->wrapBackgroundColor($badge_el, BackgroundColorEnum::LightGreen);

    $inner[] = $badge_el;

    $elements[] = $this->wrapContainerVerticalSpacingTiny($inner, AlignmentEnum::Center);

    return $this->buildInnerElementLayoutCentered($elements);
  }

}
