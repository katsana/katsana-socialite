<?php

use Katsana\Socialite\Bootstrap;
use SocialiteProviders\Manager\SocialiteWasCalled;

class BootstrapTest extends PHPUnit\Framework\TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
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
