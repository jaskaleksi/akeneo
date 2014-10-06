<?php

namespace spec\Pim\Bundle\CatalogBundle\MongoDB\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Pim\Bundle\CatalogBundle\Model\Media;

class MediaNormalizerSpec extends ObjectBehavior
{
    function it_is_a_normalizer()
    {
        $this->shouldImplement('Symfony\Component\Serializer\Normalizer\NormalizerInterface');
    }

    function it_supports_normalization_in_mongodb_json_of_media(Media $media)
    {
        $this->supportsNormalization($media, 'mongodb_json')->shouldBe(true);
        $this->supportsNormalization($media, 'json')->shouldBe(false);
        $this->supportsNormalization($media, 'xml')->shouldBe(false);
    }

    function it_normalizes_price(Media $media)
    {
        $media->getFilename()->willReturn('myfile.pdf');

        $this->normalize($media, 'mongodb_json', [])->shouldReturn(['filename' => 'myfile.pdf']);
    }
}
