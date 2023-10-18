<?php

namespace Infrastructure\OneSignal\Actions\Notification;

use Illuminate\Http\Client\Response;
use Infrastructure\OneSignal\Builders\ClientBuilder;
use Infrastructure\OneSignal\DataTransferObjects\Notification\SendExternalUserIdsData;
use Infrastructure\OneSignal\Enums\Blueprint;
use Infrastructure\OneSignal\Enums\Platform;
use Infrastructure\OneSignal\Enums\Service;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendExternalUserIdsAction extends Action
{
    public function execute(SendExternalUserIdsData $data, Platform $platform): Response
    {
        return ClientBuilder::resolve()
            ->setToken(token: Service::resolveUser(service: Service::AppKey, platform: $platform))
            ->setBaseUrl(baseUrl: Service::resolve(service: Service::Endpoint))
            ->toFactory()
            ->post(
                url: Blueprint::pushNotification(),
                data: [
                    'app_id' => Service::resolveUser(service: Service::AppId, platform: $platform),
                    'contents' => $data->notificationData->contents,
                    'headings' => $data->notificationData->headings,
                    'data' => (object) $data->notificationData->data,
                    'channel_for_external_user_ids' => $data->externalUserIdsData->channel->value,
                    'include_external_user_ids' => $data->externalUserIdsData->ids,
                ]
            );
    }
}