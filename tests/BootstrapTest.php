<?php

use Katsana\Socialite\Bootstrap;
use SocialiteProviders\Manager\SocialiteWasCalled;

class BootstrapTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_be_handled()
    {
        $socialite = Mockery::mock(SocialiteWasCalled::class);

        $socialite->shouldReceive('extendSocialite')
                ->with('katsana', 'Katsana\\Socialite\\Provider')
                ->andReturnNull();

        $this->assertNull((new Bootstrap())->handle($socialite));
    }
}
