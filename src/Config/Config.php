<?php declare(strict_types=1);

namespace Sas\SyncerModule\Config;

class Config
{
    private $base_url = "http://109.237.219.217/api";
    private $article_url = "http://109.237.219.217/api/articlefeed/";
    private $token = "wHVs3S7yMKtmvPHSVWj99naCnqdX4WaTVwCVT5rp";
    public function getApiBaseUrl(){
        return $this->base_url;
    }

    public function getArticleApiUrl(){
        return $this->article_url;
    }

    public function getApiToken(){
        return $this->token;
    }
}