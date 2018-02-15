<?php

namespace spec\Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Acme\Bundle\CustomBundle\Entity\PictogramTranslation;
use Acme\Bundle\CustomBundle\Normalizer\Standard\PictogramNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PictogramNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PictogramNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_only_normalizes_pictograms(Pictogram $pictogram)
    {
        $this->supportsNormalization(new \stdClass(), 'standard')->shouldReturn(false);
        $this->supportsNormalization(Argument::any(), 'standard')->shouldReturn(false);
        $this->supportsNormalization($pictogram, 'standard')->shouldReturn(true);
    }

    function it_only_normalizes_standard_format(Pictogram $pictogram)
    {
        $this->supportsNormalization($pictogram)->shouldReturn(false);
        $this->supportsNormalization($pictogram, 'internal_api')->shouldReturn(false);
        $this->supportsNormalization($pictogram, 'foo')->shouldReturn(false);
    }

    function it_normalizes_a_pictogram(
        Pictogram $pictogram,
        PictogramTranslation $pictogramTranslationEn,
        PictogramTranslation $pictogramTranslationFr,
        PictogramTranslation $pictogramTranslationEs
    ) {
        $pictogramTranslationEn->getLocale()->willReturn('en_US');
        $pictogramTranslationEn->getLabel()->willReturn('English');
        $pictogramTranslationFr->getLocale()->willReturn('fr_FR');
        $pictogramTranslationFr->getLabel()->willReturn('Français');
        $pictogramTranslationEs->getLocale()->willReturn('es_ES');
        $pictogramTranslationEs->getLabel()->willReturn('Español');

        $pictogram->getId()->willReturn(999);
        $pictogram->getCode()->willReturn('my_picto');
        $pictogram->getTranslations()->willReturn(
            [
                $pictogramTranslationEn,
                $pictogramTranslationFr,
                $pictogramTranslationEs,
            ]
        );

        $this->normalize($pictogram, 'standard')->shouldReturn(
            [
                'id'     => 999,
                'code'   => 'my_picto',
                'labels' => [
                    'en_US' => 'English',
                    'fr_FR' => 'Français',
                    'es_ES' => 'Español',
                ],
            ]
        );
    }
}
