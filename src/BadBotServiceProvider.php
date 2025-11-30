<?php

namespace xqus\BadBot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use xqus\BadBot\Commands\InstallRobotsTxt;
use xqus\BadBot\Commands\AddOpenAiIPsToIPTable;
use xqus\BadBot\Commands\BlockIP;
use xqus\BadBot\Commands\ClearAllIPMarks;

class BadBotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bad-bot')
            ->hasRoutes('web')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommands([
                BlockIP::class,
                InstallRobotsTxt::class,
                AddOpenAiIPsToIPTable::class,
                ClearAllIPMarks::class,                
            ]);
    }
}
