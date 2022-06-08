<?php

namespace App\Services;

use App\Exceptions\ApiValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class InstagramService
{
    /**
     * @var Client|null
     */
    private ?Client $client = null;
    private InstagramConnectService $instagramConnectsService;

    public function __construct()
    {
        $this->instagramConnectsService = new InstagramConnectService();
    }

    /**
     * @throws \App\Exceptions\ApiValidationException
     * @throws GuzzleException
     */
    public function connectInstagram($user, $code)
    {
        $date_token = $this->getAccessTokenFromCodeFacebook($code);
        $token = $date_token['access_token'] ?? null;
        if (!$token) {
            throw new ApiValidationException('Error Facebook token');
        }
        Log::info('TOKEN: ' . json_encode($date_token));
        $accounts_facebook = $this->getPageAccountsFacebook($token);
        if (!$accounts_facebook) {
            throw new ApiValidationException('Error Facebook "No accounts"');
        }
        Log::info('accounts_facebook: ' . json_encode($accounts_facebook));
        $id_account = $this->getFirstPageIdAccountFacebook($accounts_facebook);
        if (!$id_account) {
            throw new ApiValidationException('Error Facebook "No id accounts"');
        }
        Log::info('$id_account: ' . json_encode($id_account));
        $instagram_business_account_id = $this->getPageInstagramBusinessAccount($token, $id_account);
        if (!$instagram_business_account_id) {
            throw new ApiValidationException('Error: "no instagram business account"');
        }
        Log::info('$instagram_business_account_id: ' . json_encode($instagram_business_account_id));
        return $this->instagramConnectsService->updateOrCreateInstagramConnects($date_token, $user, $instagram_business_account_id);
    }

    /**
     * @param string $code
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessTokenFromCodeFacebook(string $code): array
    {
        try {
            $client = $this->getGuzzleClient();

            $url = 'https://graph.facebook.com/v13.0/oauth/access_token';

            $request_params = [
                'client_id' => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri' => config('services.facebook.redirect'),
                'code' => $code,
            ];

            $response = $client->get($url, [
                'query' => $request_params,
            ]);
            return $this->getDataFromResponse($response);

        } catch (\Exception $exception) {
            Log::info($exception);
            return [];
        }
    }

    /**
     * @param $token
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPageAccountsFacebook($token): array
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/me/accounts";
            $response = $client->get($url, [
                'query' => ['access_token' => $token],
            ]);

            return $this->getDataFromResponse($response);

        } catch (\Exception $exception) {
            Log::info($exception);
            return [];
        }

    }

    /**
     * @param $token
     * @param $page_id_account_facebook
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPageInstagramBusinessAccount($token, $page_id_account_facebook): ?string
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $page_id_account_facebook;
            $response = $client->get($url, [
                'query' => ['fields' => 'instagram_business_account', 'access_token' => $token],
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['instagram_business_account']['id'] ?? null;

        } catch (\Exception $exception) {
            Log::info('getPageInstagramBusinessAccount: ' . $exception);
            return null;
        }

    }

    /**
     * @param $accounts_facebook
     * @return int
     */
    public function getFirstPageIdAccountFacebook($accounts_facebook): int
    {
        return $accounts_facebook['data'][0]['id'] ?? 0;
    }

    /**
     * @param $instagram_id
     * @param $image_url
     * @param string $caption
     * @return int|null
     * @throws ApiValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createContainer($instagram_id, $image_url, string $caption = ''): ?int
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $instagram_id . "/media";
            $response = $client->post($url, [
                'query' => ['image_url' => $image_url, 'caption' => $caption],
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['id'] ?? 0;

        } catch (\Exception $exception) {
            Log::info('createContainer: ' . $exception);
            throw new ApiValidationException('createContainer Error');

        }
    }

    /**
     * @param $instagram_id
     * @param $creation_id
     * @return int|null
     * @throws ApiValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function publishContainer($instagram_id, $creation_id): ?int
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $instagram_id . "/media_publish";
            $response = $client->post($url, [
                'query' => ['creation_id' => $creation_id],
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['id'] ?? 0;

        } catch (\Exception $exception) {
            Log::info('publishContainer: ' . $exception);
            throw new ApiValidationException('publishContainer Error');
        }
    }

    /**
     * @throws ApiValidationException
     */
    public function publishOnePhoto($instagram_id, $image_url, $caption = '')
    {
        try {
            $id_container = $this->createContainer($instagram_id, $image_url, $caption);
            $this->publishContainer($instagram_id, $id_container);
        } catch (ApiValidationException $e) {
            throw new ApiValidationException($e);
        }

    }

    /**
     * @param $instagram_id
     * @param $image_url
     * @param $access_token
     * @return int|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createItemContainer($instagram_id, $image_url, $access_token)
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $instagram_id . "/media";
            $response = $client->post($url, [
                'query' => [
                    'image_url' => $image_url,
                    'is_carousel_item' => true,
                    'access_token' => $access_token
                ]
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['id'] ?? 0;

        } catch (\Exception $exception) {
            Log::info('createItemContainer: ' . $exception);
            return 0;
        }
    }

    /**
     * @param $instagram_id
     * @param array $children
     * @param $access_token
     * @param string $caption
     * @return int|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createCarouselContainer($instagram_id, array $children, $access_token, string $caption = '')
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $instagram_id . "/media";
            $response = $client->post($url, [
                'query' => [
                    'caption' => $caption,
                    'media_type' => 'CAROUSEL',
                    'children' => $children,
                    'access_token' => $access_token
                ]
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['id'] ?? 0;

        } catch (\Exception $exception) {
            Log::info('createCarouselContainer: ' . $exception);
            return 0;
        }
    }

    /**
     * @param $instagram_id
     * @param $creation_id
     * @param $access_token
     * @return int|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function publishCarouselContainer($instagram_id, $creation_id, $access_token)
    {
        try {
            $client = $this->getGuzzleClient();
            $url = "https://graph.facebook.com/v13.0/" . $instagram_id . "/media_publish";
            $response = $client->post($url, [
                'query' => [
                    'creation_id' => $creation_id,
                    'access_token' => $access_token
                ]
            ]);
            $resp = $this->getDataFromResponse($response);

            return $resp['id'] ?? 0;

        } catch (\Exception $exception) {
            Log::info('createCarouselContainer: ' . $exception);
            return 0;
        }
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws \JsonException
     */
    public function getDataFromResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return Client
     */
    public function getGuzzleClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

}
