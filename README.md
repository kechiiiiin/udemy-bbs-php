# アクシス新人研修用PHP＆MySQL Docker

## バージョン

- PHP: 7.4
- MySQL: 8

## ポート

サイトへのアクセス: http://localhost:48089/

phpMyAdmin: http://localhost:48088/

## Docker環境構築

Dockerを立ち上げるには以下のコマンドをターミナルで実行してください。

`docker-compose up -d`

### 主なDocker Composeのコマンド

#### 開始

`$ docker-compose up -d`

`-d` はバックグラウンドで動作するためのオプション。

このオプション無しで実行すると、実行ログを確認することができます。

#### 動作確認

`$ docker-compose ps`

#### 停止

`$ docker-compose stop`

頻繁に動かしたり止めたりする場合は、ビルド時間がかからない `stop` がおすすめ。

#### 停止して、さらに環境内のコンテナとネットワークを削除

`$ docker-compose down`

Dockerの設定を変えたときなどは、 `--rmi all` オプションをつけると、イメージも削除してくれます。

#### 再ビルドする

`$ docker-compose build`

Dockerfileを更新したときに使用。

`docker-compose down` でコンテナを破棄した後、使用してください。

`--no-cache` オプションをつけることで、ビルドの際のキャッシュを無視することができます。

#### DB(mysql)のコンテナに入る

`$ docker-compose exec db bash`

コンテナに入ったら、 `$ mysql -u root -p` でMySQLにも入れます。

パスワードは `docker-compose.yml` に記載されています。


その他のDocker Composeのコマンドは公式サイト[Overview of Docker Compose](https://docs.docker.com/compose/)を参照してください。