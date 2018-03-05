<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Versioning;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Versioning\UpdateGuesser;
use Pim\Bundle\CustomEntityBundle\Versioning\VersionableInterface;
use Pim\Bundle\VersioningBundle\UpdateGuesser\UpdateGuesserInterface;

class UpdateGuesserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UpdateGuesser::class);
    }

    function it_is_an_update_guesser()
    {
        $this->shouldImplement(UpdateGuesserInterface::class);
    }

    function it_only_supports_update_actions()
    {
        $this->supportAction(UpdateGuesserInterface::ACTION_UPDATE_ENTITY)->shouldReturn(true);
        $this->supportAction(UpdateGuesserInterface::ACTION_DELETE)->shouldReturn(false);
        $this->supportAction(UpdateGuesserInterface::ACTION_UPDATE_COLLECTION)->shouldReturn(false);
        $this->supportAction('any_other_action')->shouldReturn(false);
    }

    function it_guesses_updates_for_versionable_entities(EntityManager $em, VersionableInterface $entity)
    {
        $this->guessUpdates($em, $entity, UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
             ->shouldReturn([$entity]);

        $this->guessUpdates($em, new \stdClass(), UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
             ->shouldReturn([]);
    }
}
