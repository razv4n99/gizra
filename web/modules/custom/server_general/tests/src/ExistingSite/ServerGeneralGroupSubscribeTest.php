<?php

namespace Drupal\Tests\server_general\ExistingSite;

use Drupal\og\Og;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the group subscribe greeting for OG groups.
 */
class ServerGeneralGroupSubscribeTest extends ServerGeneralTestBase {

  /**
   * Test that a greeting and subscribe link is shown to non-member users.
   */
  public function testGroupSubscribeGreetingForNonMember() {
    // Create a group manager user and a regular user.
    $manager = $this->createUser();
    $user = $this->createUser();

    // Create a group node as the manager.
    $group_node = $this->createNode([
      'type' => 'group',
      'title' => 'Test Group',
      'moderation_state' => 'published',
      'uid' => $manager->id(),
    ]);

    // Log in as the regular user (not the group manager).
    $this->drupalLogin($user);

    // Visit the group node page.
    $this->drupalGet($group_node->toUrl());
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);

    // Assert greeting and subscribe prompt are shown.
    $this->assertSession()->pageTextContains('Hi ' . $user->getDisplayName());
    $this->assertSession()->pageTextContains('click here');
    $this->assertSession()->pageTextContains('subscribe to this group called');
    $this->assertSession()->pageTextContains('Test Group');
  }

  /**
   * Test that no greeting is shown to anonymous or member users.
   */
  public function testGroupSubscribeGreetingForMemberAndAnonymous() {
    // Create a group manager user.
    $manager = $this->createUser();

    // Create a regular user who will become a member.
    $member_user = $this->createUser();

    // Create a group node as the manager.
    $group_node = $this->createNode([
      'type' => 'group',
      'title' => 'Test Group',
      'moderation_state' => 'published',
      'uid' => $manager->id(),
    ]);

    // Create a new group membership entity and save it.
    // This approach is more reliable across different versions of OG.
    $membership = \Drupal::entityTypeManager()->getStorage('og_membership')->create([
      'type' => 'default',
      'entity_type' => 'node',
      'entity_id' => $group_node->id(),
      'uid' => $member_user->id(),
      'state' => 'active',
    ]);
    $membership->save();

    // Check if the user is a member. This is a good practice for a test.
    $this->assertTrue(Og::isMember($group_node, $member_user));

    // Visit the group page as the member user and assert no greeting.
    $this->drupalLogin($member_user);
    $this->drupalGet($group_node->toUrl());
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
    // Assert the greeting is not present.
    $this->assertSession()->pageTextNotContains('Hi ' . $member_user->getDisplayName());
    $this->assertSession()->pageTextNotContains('click here');

    // Log out the user.
    $this->drupalLogout();

    // Visit the group page as an anonymous user and assert no greeting.
    $this->drupalGet($group_node->toUrl());
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
    // Assert the greeting is not present.
    $this->assertSession()->pageTextNotContains('click here');
  }

}
