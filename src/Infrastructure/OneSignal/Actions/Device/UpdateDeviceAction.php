<?php

namespace Infrastructure\OneSignal\Actions\Device;

use Domain\Shared\User\Models\User;
use Illuminate\Http\Client\Response;
use Infrastructure\OneSignal\Builders\ClientBuilder;
use Infrastructure\OneSignal\DataTransferObjects\Device\UpdatedDeviceData;
use Infrastructure\OneSignal\DataTransferObjects\Device\UpdateDeviceData;
use Infrastructure\OneSignal\Enums\Blueprint;
use Infrastructure\OneSignal\Enums\NotificationType;
use Infrastructure\OneSignal\Enums\Service;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UpdateDeviceAction extends Action
{
    public function execute(UpdateDeviceData $data, User $user): mixed
    {
        return value(
            fn (Response $response) => UpdatedDeviceAction::resolve()
                ->execute(
                    data: UpdatedDeviceData::resolveFromResponse(response: $response),
                    deviceData: $data->deviceData,
                    user: $user,
                ),

            response: ClientBuilder::resolve()
                ->setToken(token: null)
                ->setBaseUrl(baseUrl: Service::resolve(service: Service::Endpoint))
                ->toFactory()
                ->put(
                    url: Blueprint::editDevice($data->deviceData->playerId),
                    data: [
                        'app_id' => Service::resolveUser(service: Service::AppId, platform: $data->deviceData->user),
                        'notification_types' => NotificationType::Subscribed->value,
                        'external_user_id' => $data->externalUserId ?? $user->email,
                    ]
                )
        );
    }
}