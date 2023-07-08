<?php

namespace LaravelGoogleCalendar\Infra\Providers;

use Google\Service\Drive;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use LaravelGoogleCalendar\Application\Ports\ConfigContract;
use LaravelGoogleCalendar\Application\Ports\GoogleCalendarContract;
use LaravelGoogleCalendar\Domain\Exceptions\CredentialException;
use LaravelGoogleCalendar\Infra\Adapters\Config;
use LaravelGoogleCalendar\Infra\Adapters\GoogleCalendar as GoogleCalendarAdapter;
use LaravelGoogleCalendar\Infra\Handlers\GoogleCalendar as GoogleCalendarHandler;

class LaravelGoogleCalendarServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../../../config/google_calendar.php' => config_path(
                    'google_calendar.php'
                ),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/google_calendar.php',
            'google_calendar'
        );
    }

    public function register(): void
    {
        $this->registerGoogleClient();
        $this->registerGoogleServiceDrive();
        $this->registerGoogleCalendarAdapter();
        $this->registerConfigAdapter();
        $this->registerGoogleCalendarHandler();
    }

    /**
     * @return array<int,string>
     */
    public function provides(): array
    {
        return [
            Google_Service_Drive::class,
            Google_Client::class,
            GoogleCalendarContract::class,
            ConfigContract::class,
            GoogleCalendarHandler::class,
        ];
    }

    /**
     * @return array<string,string>
     * @throws CredentialException
     */
    private function getCredentials(): array
    {
        $credentialsFilePath = config(
            'google_calendar.credentials.service_account'
        );

        if (empty($credentialsFilePath)) {
            throw new CredentialException(
                'Credential data not found. Please check the GOOGLE_APPLICATION_CREDENTIALS env variable.'
            );
        }

        $credentialsFileContent = file_get_contents($credentialsFilePath);

        return json_decode($credentialsFileContent ?: '', true) ?: [];
    }

    /**
     * @return void
     */
    private function registerGoogleClient(): void
    {
        $this->app->bind(Google_Client::class, function () {
            $client = new Google_Client();
            $client->addScope(Drive::DRIVE);
            $credentials = $this->getCredentials();

            if (empty($credentials)) {
                throw new CredentialException(
                    'Credential data not found. Please check the service account file content.'
                );
            }

            $client->setAuthConfig($credentials);

            return $client;
        });
    }

    /**
     * @return void
     */
    private function registerGoogleServiceDrive(): void
    {
        $this->app->bind(
            Google_Service_Drive::class,
            function (Application $app) {
                $client = $app->make(Google_Client::class);
                $googleServiceDrive = new Google_Service_Drive($client);
                $googleServiceDrive->servicePath = config(
                    'google_calendar.folder_id'
                );

                return $googleServiceDrive;
            }
        );
    }

    /**
     * @return void
     */
    private function registerGoogleCalendarAdapter(): void
    {
        $this->app->bind(
            GoogleCalendarContract::class,
            function (Application $application) {
                $service = $application->make(Google_Service_Drive::class);

                return new GoogleCalendarAdapter($service);
            }
        );
    }

    /**
     * @return void
     */
    private function registerGoogleCalendarHandler(): void
    {
        $this->app->bind('googleDrive', function (Application $application) {
            return $application->make(GoogleCalendarHandler::class);
        });
    }

    private function registerConfigAdapter(): void
    {
        $this->app->bind(ConfigContract::class, function () {
            $config = $this->app->make(Repository::class);

            return new Config($config);
        });
    }
}
