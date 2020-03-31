# 利用用途に応じたアプリケーションの機能の修正方法について(開発者向け情報)

## 説明

>「探究学習地図アプリ」ではアプリケーション内に設置している設定ファイルを変更することで利用用途に応じたカスタマイズが可能です。各機能の修正方法については後述の設定内容をご確認ください。
>>※設定の反映方法については設定修正後アプリケーションのビルド作業を行いビルドにより生成されたHTML/CSS/JavaScriptファイルをWebサイトの領域にアップロードすることで修正内容が反映されます。

#### ファイルの構成について
>本アプリケーションは以下の構成にてフロントエンド部分のアプリケーションの機能を提供しています。

```
src以下のディレクトリ構成（/src/srcディレクトリ）
├─assets         ‥‥‥‥画像素材管理
├─components     ‥‥‥‥各画面機能のプログラム領域
│  ├─common
│  └─geoapp
│      ├─about
│      ├─basemap
│      ├─data
│      ├─datalayer
│      ├─datamap
│      ├─login
│      ├─mapedit
│      ├─portal
│      ├─registration
│      └─startup
├─config         ‥‥‥‥アプリケーションの設定ファイル領域
├─css            ‥‥‥‥CSSファイル領域
├─router         ‥‥‥‥機能ごとのURLを管理
└─utility        ‥‥‥‥アプリケーションで利用するライブラリを管理
    ├─asset
    ├─datahelper
    ├─gishelper
    └─requesthelper
```

>アプリケーション全体の設定はsrc/config以下の設定ファイルにて設定変更を行えます。
各種画像の変更についてはsrc/assetsディレクトリ以下のファイルを変更します。


#### 設定ファイルの構成について
>設定ファイルの構成については以下の構成で管理しています。

```
管理ディレクトリ：src/config

ファイル名
api.json        :サーバへの接続情報やAPI接続情報を記載したファイル
app.config.json :アプリケーション全体の設定を管理する設定ファイル
app.label.json  :アプリケーション全体の文言・ラベルを管理する設定ファイル
dataset_format.json:施設情報などのデータセットをCSVファイルより設定する際のCSVレイアウト定義ファイル
```

#### 全体のデザイン変更について
>デザイン変更については初期表示画像（スプラッシュ画像）やアプリの各種タイトルアイコン画像を差し替え・変更することで利用用途に応じた画像の変更が可能です。

・設定方法について
以下のディレクトリのファイル名を上書きすることで変更が可能です。
```
タイトルファイル：src/assets/common/title.png
スプラッシュ画像：src/assets/startup/startup.png
```


#### サーバへの接続設定について
>このアプリケーションはデータの保存をサーバ側で行っておりますAPIを経由しデータの保存などを行っております。
接続先の設定についてはapi.jsonを参照し以下の箇所を利用用途に応じて修正してください。
```
    サーバの接続設定を変更する場合はapi.json内のapi_common_infoのhost項目の値を接続するサーバのURL形式で設定してください。
    //設定箇所
    "api_common_info" : {
        "host": "http://サーバのURL"
    },
```
#### アプリケーション全体の文言・文章の設定について
>アプリケーション全体の文言・ラベルの設定では各画面の文章を変更することが可能です。
文章の変更についてはapp.label.jsonを参照し利用用途に応じて修正してください。


#### アプリケーション全体の設定について
>アプリケーション全体の設定では地図情報の表示する際のデフォルトの位置情報や利用するメニュー情報の設定が可能です。
アプリケーションの全体の設定についてはapp.config.jsonを参照し利用用途に応じて修正してください。

```
app.config.jsonの設定ファイルでは以下の内容を設定変更することが出来ます。
・各機能メニューの設定
・地図アプリ上のデフォルト操作の設定（起点の緯度経度・ズームなど）
・データ書き込み機能の書き込み用アイコンの追加・削除などの設定
・アプリ上で利用するグループの設定
・ベースマップ切り替え機能における選択する地図タイル情報の追加・削除などの設定
・データレイヤーで選択する地図タイル情報の追加・削除などの設定
```
#### 地図アプリ上の操作設定について
地図アプリ上の操作設定についてapp.config.jsonの設定ファイルの以下の項目を修正します。

1. 地図アプリを利用するうえでの地図上の起点の緯度経度・ズーム設定については
map_configを修正することで変更が可能です。
```
    設定箇所
    ・・・
    "map_config": {
      "latitude": 33.728311,
      "longitude": 130.972983,
      "zoomlevel": 13,
      "maxzoomlevel": 18,
      "portal_map_panel": "gsi_base_map"
    }
    map_config以下の各項目説明
      "latitude": ※起点となる緯度
      "longitude": ※起点となる経度
      "zoomlevel": ※デフォルトズームレベル
      "maxzoomlevel": ※MAXズームレベル
      "portal_map_panel": ※デフォルトの地図パネル（設定値はmap_panel_infoのユニーク値を設定）

```
2. データ書き込み機能でのアイコンの削除などの設定についてはmap_edit_type以下の項目設定します。
```
    "map_edit_type": {
        "marker_menu": {
            "marker_1": {"type": "marker", "name":"情報", "image_type": "information"},
            "edit_polyline": {"type": "polyline", "name":"線", "image_type": "edit_line"},
            "edit_circle": {"type": "circle", "name":"円", "image_type": "edit_circle"}
        }
    }
```

#### ベースマップについて
>ベースマップに利用される地図タイルについては設定ファイルを追加修正することにより変更が可能です。

```
・設定方法について
app.config.jsonの設定ファイルのmap_panel_info以下の項目を設定変更することで地図タイルの追加削除が出来ます。
    設定箇所
    ・・・
    "map_panel_info": {
        ※以下地図タイル設定情報
        "osm": {
            "name": "open street map",
            "panel": "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            "attribution": "Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a>"
        },
        "gsi_base_map": {
            "name": "国土地理院 地図",
            "panel": "https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png",
            "attribution": "Map data &copy; <a href=\"https://maps.gsi.go.jp/development/ichiran.html\">国土地理院</a>"
        },
    ・・・

    map_panel_info以下の各項目説明
    "ユニーク値": {     ※他と被らない値を設定します。
        "name":        ※アイコンの名称を指定します。
        "image":       ※アイコン画像を指定します。
        "panel":       ※地図タイルのURLを指定します。
        "attribution": ※データの帰属元を記載します。
    }

```

#### データレイヤーについて
>データレイヤー（防災データ）に利用される地図タイルについては設定ファイルを追加修正することにより変更が可能です。

```
・設定方法について
app.config.jsonの設定ファイルのmap_datalayter_info以下の項目を設定変更することで地図タイルの追加削除が出来ます。
    設定箇所
    ・・・
    "map_datalayter_info": {
        ※以下地図タイル設定情報
        "flood": {
            "name": "国管理河川_洪水浸水想定区域",
            "image":"kouzui",
            "panel": "https://disaportaldata.gsi.go.jp/raster/01_flood_l2_shinsuishin_kuni_data/{z}/{x}/{y}.png",
            "attribution": "国土交通省　各地方整備局"
        },
    ・・・

    map_datalayter_info以下の各項目説明
    "ユニーク値": {     ※他と被らない値を設定します。
        "name":        ※アイコンの名称を指定します。
        "image":       ※アイコン画像を指定します。
        "panel":       ※地図タイルのURLを指定します。
        "attribution": ※データの帰属元を記載します。
    }

```

#### POIデータインポートでのCSVデータの追加について
>データインポートに登録されるCSVデータについてはdataset_format.jsonの設定ファイルで登録するCSVファイルのフォーマットのレイアウトを設定することにより利用が可能です。

```
・設定方法について

    設定箇所
    ・・・
    "EMERGENCYSHELTER" : {
        "name": "指定緊急避難場所",
        "description": "市区町村から提供される指定緊急避難場所の一覧。「10.指定緊急避難場所一覧」シートを参照。",
        "URL": "https://cio.go.jp/sites/default/files/uploads/documents/opendata_suisyou_dataset_teigisyo.xlsx",
        "image_type": "EMERGENCYSHELTER",
        "format_mode": "inflexible",
        "format": [
            {"name": "NO", "required": false, "item_name": "no", "format_type": "string", "placeholder":""},
            {"name": "名称", "required": false, "item_name": "name", "format_type": "string", "placeholder":""},
            {"name": "名称_カナ", "required": false, "item_name": "name_kana", "format_type": "string", "placeholder":""},
            {"name": "住所", "required": false, "item_name": "address", "format_type": "string", "placeholder":""},
            {"name": "方書", "required": false, "item_name": "katagaki", "format_type": "string", "placeholder":""},
            {"name": "緯度", "required": false, "item_name": "latitude", "format_type": "latitude", "placeholder":""},
            {"name": "経度", "required": false, "item_name": "longitude", "format_type": "longitude", "placeholder":""},
            {"name": "標高", "required": false, "item_name": "elevation", "format_type": "string", "placeholder":""},
            {"name": "電話番号", "required": false, "item_name": "phone_number", "format_type": "string", "placeholder":""},
    ・・・
    "ユニーク値": {     ※他と被らない値を設定します。
        "name":        ※インポート定義の名称を指定します。
        "description": ※インポート定義の説明を指定します。
        "URL":         ※インポート定義などの参考URLを指定します。
        "image_type":  ※データ登録画面のアイコンを指定します。
        "format_mode": ※CSVのフォーマットが固定型か可変長型かを指定（※現在は固定長型(inflexible)のみ)
        "format":[      ※CSVのカラムフォーマットを定義(配列による定義)
          ※CSVのカラムフォーマット（1列あたりの定義
          {"name":      ※カラム名
          "required":   ※必須かどうか　必須時 true
          "item_name":  ※カラム名（英名：半角英数字で入力
          "format_type":※カラムのフォーマット緯度経度情報は「latitude」,「longitude」を指定
          "placeholder":※補足文
          },
          ・・カラム数分設定・・
    }


```

#### ユーザの作成方法について
>ユーザの追加については設定ファイルを変更することでユーザの登録画面にアクセスすることが出来ます。設定ファイルを変更しユーザ登録画面よりユーザを登録してください。
>>※ユーザ登録時のグループの追加・変更についてはグループの追加・変更項目を参照ください。

```
・設定方法について
ユーザの追加はapp.config.jsonの設定ファイルのapp_config以下のuser_regist項目を'on'に設定変更することでユーザ登録画面が利用できるようになります。

    設定箇所
    ・・・
    "app_config": {
        "user_regist": "off" ← on に変更
    },
    ・・・
変更後
https://アプリのURL/#/registration
よりユーザ登録画面へのアクセスが可能になりますのでユーザ登録画面よりユーザの作成を行ってください。

```

#### グループの追加・変更について

>グループは「データインポート」や「データ書き込み機能」における共有利用を行うためのグループ機能です。
利用にあたってグループの追加についてはapp.config.jsonの設定ファイルを変更することで追加・削除が可能です。


```
・設定方法について
ユーザの追加はapp.config.jsonの設定ファイルのapp_config以下のuser_group項目に追加・変更することでグループの追加・削除が可能です。

    設定箇所
    ・・・
    "user_group": [
        {"name":"デモグループ01", "value":"DEMO0001"},
        {"name":"デモグループ02", "value":"DEMO0002"},
        {"name":"デモグループ03", "value":"DEMO0003"},
        ・・・
    ],
    ・・・
    変更後
    https://アプリのURL/#/registration
    のユーザ登録時にグループの選択が可能です。

```
