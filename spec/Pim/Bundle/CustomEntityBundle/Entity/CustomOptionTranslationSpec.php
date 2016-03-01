<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOption;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOptionTranslation;

class CustomOptionTranslation extends AbstractCustomOptionTranslation
{
    public function getCustomEntityName()
    {
        return 'custom_option_translation_name';
    }
}

class CustomOptionTranslationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomOptionTranslation');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOptionTranslation');
    }
}
