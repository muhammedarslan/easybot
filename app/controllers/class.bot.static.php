<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Url\Url;

class EasyBotStaticContent extends EasyBotRestrictions
{

    protected object $Db;
    protected array  $UserQuery;
    protected array  $Request;
    protected string $ErrorMessage;
    protected array  $RequestHeaders;
    protected string $RequestBody;
    protected array  $RedirectList   = [];
    protected bool   $IsRequestValid = true;

    public function setDb($db): void
    {
        $this->Db = $db;
    }

    public function setUser($Query): void
    {
        $this->UserQuery = $Query;
    }

    public function setRequest($Request): void
    {
        $this->Request = $Request;
    }

    private function onRedirect($FromUrl, $RedirectedUrl): void
    {
        $url = Url::fromString($RedirectedUrl);
        array_push($this->RedirectList, $RedirectedUrl);

        if (!parent::Domain($url->getHost())) {
            throw new Exception(StaticFunctions::lang('EasyBot politikaları gereği bu alan adı kullanılamıyor: {0}', [
                $url->getHost()
            ]));
        }

        if (!parent::Path($url->getPath())) {
            throw new Exception(StaticFunctions::lang('EasyBot politikaları gereği bu dizin kullanılamıyor: {0}', [
                $url->getPath()
            ]));
        }

        if (!parent::Parametres($url->getQuery())) {
            throw new Exception(StaticFunctions::lang('EasyBot politikaları gereği bu parametre kullanılamıyor: {0}', [
                $url->getQuery()
            ]));
        }
    }

    private function EasyBotRayId(): string
    {
        $RayID = md5(StaticFunctions::random_with_time(32) . $this->userID());

        $InsertRay = $this->Db->prepare("INSERT INTO easybot_ray_logs SET
        user_id = ?,
        user_info = ?,
        bot_type = ?,
        bot_domain = ?,
        bot_url = ?,
        bot_data = ?,
        bot_time = ?,
        ray_id = ?");
        $Inserted = $InsertRay->execute(array(
            $this->userID(), json_encode([
                'userPhone' => $this->UserQuery['phone_code'] . $this->UserQuery['phone_number'],
                'phoneVerify' => $this->UserQuery['phone_verify'],
                'userBalance' => $this->UserQuery['balance'],
                'lastLogin' => json_decode($this->UserQuery['last_login']),
                'userIp' => StaticFunctions::get_ip(),
                'browser' => StaticFunctions::getBrowser()
            ]), 'create_bot_static', $this->Request['Url']['host'], $this->Request['Url']['url'], json_encode($this->Request), time(), $RayID
        ));

        if (!$Inserted) {
            throw new Exception(StaticFunctions::lang('Sistemsel bir hata meydana geldi.'));
        }

        return $RayID;
    }

    private function requestBody(): array
    {

        switch ($this->Request['Body']['bodyType']) {
            case 'form-data':
                return [
                    'name' => 'multipart',
                    'value' => $this->Request['Body']['bodyValue']
                ];
                break;
            case 'x-www-form-urlencoded':
                return [
                    'name' => 'form_params',
                    'value' => $this->Request['Body']['bodyValue']
                ];
                break;
            case 'raw':
                return [
                    'name' => 'body',
                    'value' => $this->Request['Body']['bodyValue']
                ];
                break;

            default:
                return [
                    'name' => 'http_errors',
                    'value' => false
                ];
                break;
        }
    }

    private function requestCookie(): object
    {
        $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
        foreach ($this->Request['Cookies'] as $key => $cookie) {
            $cookieJar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
                'Domain'  => $cookie['domain'],
                'Name'    => $key,
                'Value'   => $cookie['value']
            ]));
        }

        return $cookieJar;
    }

    private function requestHeaders($RayID): array
    {
        $Headers = [];
        foreach ($this->Request['Headers'] as $key => $header) {
            if (is_array($header)) {
                $Headers[$key] = $header['value'];
            } else {
                $Headers[$key] = $header;
            }
        }

        $Headers['Cache-Control'] = 'no-cache';
        $Headers['Accept'] = '*/*';
        $Headers['Connection'] = 'keep-alive';
        $Headers['Easybot-Ray'] = $RayID;

        return $Headers;
    }

    private function requestQueries(): array
    {
        $Queries = [];
        foreach ($this->Request['UrlParams'] as $key => $query) {
            if (is_array($query)) {
                $Queries[$key] = $query['value'];
            } else {
                $Queries[$key] = $query;
            }
        }

        return $Queries;
    }

    private function requestProxy(): string
    {
        switch ($this->Request['ProxyType']) {
            case 'datacenter':
                return 'http://' . ProjectDefines::LuminatiProxy()['user'] . '-country-' . $this->Request['ProxyCountry'] . ':' . ProjectDefines::LuminatiProxy()['password'] . '@' . ProjectDefines::LuminatiProxy()['proxy'] . ':' . ProjectDefines::LuminatiProxy()['port'];
                break;
        }
    }

    private function responseStatusCode($Code): void
    {
        if (HttpStatusCodes::isError($Code) == true) {
            throw new Exception(StaticFunctions::lang('Http Hatası:') . ' ' . HttpStatusCodes::getMessageForCode($Code));
        }

        if (HttpStatusCodes::canHaveBody($Code) == false) {
            throw new Exception(StaticFunctions::lang('Body Bulunamadı:') . ' ' . HttpStatusCodes::getMessageForCode($Code));
        }
    }

    private function setResponseInformation($RayID, $Headers, $StatusCode, $BodySize): void
    {
        $InsertArray = [
            'HttpStatus' => $StatusCode,
            'HttpHeaders' => $Headers,
            'BodySize'   => $BodySize
        ];
        $UserID = $this->userID();

        $UpdateRayInfo = $this->Db->prepare("UPDATE easybot_ray_logs SET
        bot_response_information = :info_json
            WHERE ray_id = :ray_id and user_id=:usid ");
        $IsUpdated = $UpdateRayInfo->execute(array(
            "info_json" => json_encode($InsertArray),
            "ray_id" => $RayID,
            'usid' => $UserID
        ));

        if (!$IsUpdated) {
            throw new Exception(StaticFunctions::lang('Sistemsel bir hata meydana geldi.'));
        }
    }

    private function rayStorage($RayID, $Body): void
    {
        file_put_contents(APP_DIR . '/tmp/easybot_ray_storage/' . $RayID . '.easybot', $Body);
        $RequestBodyFile = APP_DIR . '/tmp/easybot_ray_storage/' . $RayID . '.easybot';

        $SecurityClass = new Upload();

        if (!$SecurityClass->IsFileSecure($RequestBodyFile)) {
            throw new Exception(StaticFunctions::lang('Bu istek güvenlik algoritması tarafından zararlı olarak algılandı. Bunun bir hata olduğunu düşünüyorsan lütfen bize bildir.'));
        }
    }

    public function sendRequest(): void
    {
        try {
            $client = new \GuzzleHttp\Client();
            $RayID = $this->EasyBotRayId();
            $response = $client->request(
                mb_strtoupper($this->Request['RequestType']),
                $this->Request['Url']['url'],
                [
                    'on_headers' => function (ResponseInterface $response) {
                        if ($response->getHeaderLine('Content-Length') > 2048) {
                            throw new \Exception(StaticFunctions::lang('Body boyutu 2mb\'dan büyük olamaz.'));
                        }
                    },
                    'http_errors' => false,
                    'allow_redirects' => [
                        'max'             => 5,
                        'strict'          => false,
                        'referer'         => false,
                        'protocols'       => ['http', 'https'],
                        'on_redirect'     => function (
                            RequestInterface $request,
                            ResponseInterface $response,
                            UriInterface $uri
                        ) {
                            $this->onRedirect($request->getUri(), $uri);
                        },
                        'track_redirects' => false
                    ],
                    $this->requestBody()['name'] => $this->requestBody()['value'],
                    'cookies' => $this->requestCookie(),
                    'headers' => $this->requestHeaders($RayID),
                    'proxy' => $this->requestProxy(),
                    'query' => $this->requestQueries(),
                    'timeout' => 60
                ]
            );

            $HttpStatusCode = $response->getStatusCode();
            $this->responseStatusCode($HttpStatusCode);

            $Body = $response->getBody();
            $BodySize = $Body->getSize();
            if ($BodySize > 2097152) {
                throw new \Exception(StaticFunctions::lang('Body boyutu 2mb\'dan büyük olamaz.'));
            }

            $this->RequestBody = $Body;
            $this->RequestHeaders = $response->getHeaders();
            $this->setResponseInformation($RayID, $response->getHeaders(), $HttpStatusCode, $BodySize);
            $this->rayStorage($RayID, $Body);
        } catch (Exception  $th) {
            $this->ErrorMessage = $th->getMessage();
            $this->IsRequestValid = false;
        } catch (Throwable $th) {
            $this->ErrorMessage = $th->getMessage();
            $this->IsRequestValid = false;
        }
    }

    public function getBody(): string
    {
        return $this->RequestBody;
    }

    public function getHeaders(): array
    {
        return $this->RequestHeaders;
    }

    public function userID(): int
    {
        return $this->UserQuery['id'];
    }

    public function RequestValid(): bool
    {
        return $this->IsRequestValid;
    }

    public function GetErrors(): string
    {
        $ErrorMessage = $this->ErrorMessage;
        if ($ErrorMessage == 'An error was encountered during the on_headers event') {
            $ErrorMessage = StaticFunctions::lang('Maksimum kabul edilebilen istek boyutu 2mb ile sınırlıdır.');
        }

        return $ErrorMessage;
    }
}