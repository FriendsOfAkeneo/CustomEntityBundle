<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomOption;

class TranslatableCustomOption extends AbstractTranslatableCustomOption
{
    public function getCustomEntityName()
    {
        return 'custom_option_name';
    }

    public function getTranslationFQCN()
    {
        return 'translation_fqcn';
    }
}

class TranslatableCustomOptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\TranslatableCustomOption');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomOption');
    }
}
