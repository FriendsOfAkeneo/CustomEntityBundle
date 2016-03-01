<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

class TranslatableCustomEntity extends AbstractTranslatableCustomEntity
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

class TranslatableCustomEntitySpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\TranslatableCustomEntity');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity');
    }

    function it_is_translatable()
    {
        $this->shouldImplement('Akeneo\Component\Localization\Model\TranslatableInterface');
    }
}
