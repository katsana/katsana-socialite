<?php

namespace Katsana\Socialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class Bootstrap
{
    /**
     * Register the socialite provider.
     *
     * @param  \SocialiteProviders\Manager\SocialiteWasCalled  $event
     *
     * @return void
     */
    public function handle(SocialiteWasCalled $event)
    {
        $event->extendSocialite('katsana', __NAMESPACE__.'\\Provider');
    }
}
