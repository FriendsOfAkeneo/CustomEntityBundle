<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\TranslatableInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

class TranslatableCustomOption extends AbstractTranslatableCustomEntity
{
    public function getCustomEntityName(): string
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
        $this->beAnInstanceOf(TranslatableCustomOption::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AbstractCustomEntity::class);
    }

    function it_is_translatable()
    {
        $this->shouldImplement(TranslatableInterface::class);
    }
}
