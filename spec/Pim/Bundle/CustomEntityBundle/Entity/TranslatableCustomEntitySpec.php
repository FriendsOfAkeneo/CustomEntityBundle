<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use Acme\Bundle\CustomBundle\Entity\PictogramTranslation;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

class TranslatableCustomEntity extends AbstractTranslatableCustomEntity
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_name';
    }

    public function getTranslationFQCN()
    {
        return PictogramTranslation::class;
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

    function it_gets_a_translation()
    {
        $this->getTranslationFQCN();
        $this->getTranslation('en_US')->shouldHaveType(PictogramTranslation::class);
    }

    function it_gets_null_translation_when_no_locale()
    {
        $this->getTranslationFQCN();
        $this->getTranslation(null)->shouldReturn(null);
    }
}
