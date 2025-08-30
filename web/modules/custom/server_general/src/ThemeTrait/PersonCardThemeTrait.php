<?php

declare(strict_types=1);

namespace Drupal\server_general\ThemeTrait;

use Drupal\Core\Url;
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
      BackgroundColorEnum::LightGreen => 'bg-green-100',
    };

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
    string $badge,
    string $email,
    string $phone
  ): array {
    // Main content (image, name, detail, badge).
    $main_inner = [];

    // Image.
    $main_inner[] = $this->wrapRoundedCornersFull([
      '#theme' => 'image',
      '#uri' => $image_url,
      '#alt' => $alt,
      '#width' => 128,
    ]);

    // Name (bold).
    $name_el = $this->wrapTextFontWeight($name, FontWeightEnum::Bold);
    $main_inner[] = $this->wrapTextCenter($name_el);

    // Detail (sm, gray).
    $detail_el = $this->wrapTextResponsiveFontSize($detail, FontSizeEnum::Sm);
    $detail_el = $this->wrapTextCenter($detail_el);
    $main_inner[] = $this->wrapTextColor($detail_el, TextColorEnum::Gray);

    // Badge (xs)
    $badge_el = $this->wrapTextResponsiveFontSize($badge, FontSizeEnum::Xs);
    $badge_el = $this->wrapTextCenter($badge_el);
    $badge_el = $this->wrapTextColor($badge_el, TextColorEnum::DarkGreen);
    $badge_el = $this->wrapBackgroundColor($badge_el, BackgroundColorEnum::LightGreen);
    $main_inner[] = $badge_el;

    // Email and phone row, each wrapped in a div with icon.
    $email_row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['flex', 'items-center', 'gap-2', 'pt-4', 'pb-4']],
      'icon' => [
        '#markup' => '<i class="fa fa-envelope text-gray-400"></i>',
      ],
      'link' => [
        '#type' => 'link',
        '#title' => $this->wrapTextColor(
          $this->wrapTextResponsiveFontSize('Email', FontSizeEnum::Sm),
          TextColorEnum::DarkGray
        ),
        '#url' => Url::fromUri('mailto:' . $email),
      ],
    ];

    // Divider span.
    $divider = [
      '#markup' => '<span class="mx-4 self-stretch border-r border-gray-200"></span>',
    ];

    // Phone row.
    $phone_row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['flex', 'items-center', 'gap-2', 'pt-4', 'pb-4']],
      'icon' => [
        '#markup' => '<i class="fa fa-phone text-gray-400"></i>',
      ],
      'link' => [
        '#type' => 'link',
        '#title' => $this->wrapTextColor(
          $this->wrapTextResponsiveFontSize('Call', FontSizeEnum::Sm),
          TextColorEnum::DarkGray
        ),
        '#url' => Url::fromUri('tel:' . $phone),
      ],
    ];

    // Contact details container with separator and divider.
    $links_row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['w-full', 'flex', 'flex-col', 'mt-4']],
      'separator' => [
        '#theme' => 'server_theme_line_separator',
        '#attributes' => ['class' => ['w-full']],
      ],
      'links' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['flex', 'justify-center', 'items-stretch', 'w-full', 'gap-10']],
        'email' => $email_row,
        'divider' => [
          '#markup' => '<span class="mx-4 border-r border-gray-200 self-stretch"></span>',
        ],
        'phone' => $phone_row,
      ],
    ];

    // Outer wrapper: w-full flex flex-col gap-3 md:gap-5 items-center
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['w-full', 'flex', 'flex-col', 'gap-3', 'md:gap-5', 'items-center', 'relative', 'rounded-lg', 'border', 'border-gray-300', 'pt-10']],
      'main' => $this->wrapContainerVerticalSpacingTiny($main_inner, AlignmentEnum::Center),
      'contacts' => $links_row,
    ];
  }
}