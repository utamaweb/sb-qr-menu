<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use App\Models\Config;
use App\Models\CustomMessage;
use Carbon\Carbon;

// test commit to merge branch
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        // Schema::defaultStringLength(191);
        Blade::directive('currency', function ( $expression ) { return "Rp. <?php echo number_format($expression,0,',','.'); ?>"; });

        if (env('APP_ENV') === 'development' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
            $this->app['request']->server->set('SERVER_PORT', 443);
        }

        // Custom configs
        $configs = Config::all();
        $customMessages = CustomMessage::all();

        foreach ($configs as $config) {
            config()->set('app_config.' . $config->key, $config->value);
        }

        foreach ($customMessages as $customMessage) {
            config()->set('custom_message.' . $customMessage->key, $customMessage->value);
        }

        // Set carbon locale
        Carbon::setLocale('id');

    }
}
