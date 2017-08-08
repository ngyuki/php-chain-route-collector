# php-route-collector

[![Build Status](https://travis-ci.org/ngyuki/php-route-collector.svg?branch=master)](https://travis-ci.org/ngyuki/php-route-collector)
[![Coverage Status](https://coveralls.io/repos/github/ngyuki/php-route-collector/badge.svg?branch=master)](https://coveralls.io/github/ngyuki/php-route-collector?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ngyuki/route-collector/version)](https://packagist.org/packages/ngyuki/route-collector)
[![Latest Unstable Version](https://poser.pugx.org/ngyuki/route-collector/v/unstable)](//packagist.org/packages/ngyuki/route-collector)
[![License](https://poser.pugx.org/ngyuki/route-collector/license)](https://packagist.org/packages/ngyuki/route-collector)

ルート定義をメソッドチェインでできるようにしてはどうかと思って作ったサンプル。

## Example

```php
$registry = new RouteRegistry();
$r = new RouteCollector($registry);

// GET / -> HomeController::index
$r->path('/')->get()->controller('HomeController')->action('index');

// GET|POST /both -> HomeController::both
$r->path('/both')->get()->post()->controller('HomeController')->action('both');

// GET|POST /both -> HomeController::method
$r->path('/method')->method('GET|POST')->controller('HomeController')->action('method');

$r->controller('UserController')->group(function (RouteCollector $r) {
    $r->path('/user')->group(function (RouteCollector $r) {

        // GET /user -> UserController::index
        $r->get()->action('index');

        // GET /user/create -> UserController::create
        $r->path('/create')->get()->action('create');

        // POST /user/create -> UserController::store
        $r->path('/create')->post()->action('store');
    });
    $r->path('/user/{id}')->group(function (RouteCollector $r) {

        // GET /user/{id} -> UserController::show
        $r->get()->action('show');

        // GET /user/{id}/edit -> UserController::edit
        $r->path('/edit')->get()->action('edit');

        // PUT /user/{id}/edit -> UserController::update
        $r->path('/edit')->put()->action('update');

        // DELETE /user/{id}/edit -> UserController::delete
        $r->path('/edit')->delete()->action('delete');
    });
});

return $registry->getRoutes();
```

## 詳細

メソッドチェインを用いた内部 DSL からルート定義の配列を作成するライブラリです。

以下のメソッドでルートを定義します。

- `path(string $path)`
    - URL のリクエストパス
- `get()/post()/put()/patch()/delete()/method(string $method)`
    - リクエストメソッド
- `params(array $params)`
- `param(string $name, mixed $value)`
    - ルートパラメータ

これら以外の名前でマジックメソッドが呼ばれると `param($name, $value)` に置き換えられます。よって、下記の２つの呼び出しは等価です。

```php
$r->controller('HomeController')->action('index');
$r->param('controller', 'HomeController')->param('action', 'index');
```

チェインが途切れたところで１つのルートが登録されます。

```php
// これで１つのルート定義
$r->path('/')->get()->controller('HomeController')->action('index');
```

チェインの順番を入れ替えても同じ結果になります。

```php
// ↑と同じルート定義
$r->action('index')->controller('HomeController')->get()->path('/');
```

リクエストメソッドのチェインを複数つなげると複数のリクエストメソッドにマッチします。

```php
// GET と POST の両方にマッチします
$r->path('/')->get()->post()->controller('HomeController')->action('index');
```

リクエストメソッドは `method()` で文字列リテラルでパイプ区切りでも指定できます。

```php
// GET と POST の両方にマッチします
$r->path('/')->method('GET|POST')->controller('HomeController')->action('index');
```

`path` のチェインを複数つなげるとパスが連結されます。

```php
// '/aaa/bbb' にマッチします
$r->path('/aaa')->path('/bbb')->get()->controller('HomeController')->action('index');
```

`params` のチェインはマージされます。

```php
// params(['aaa' => 9, 'bbb' => 2, 'ccc' => 3]) とマージされる
$r->path('/')->get()
    ->params(['aaa' => 1])
    ->params(['aaa' => 9, 'bbb' => 2])
    ->params(['ccc' => 3]);
```

`group` で途中まで定義されたルート定義を元に複数のルート定義ができます。

でチェインの途中で複数のルートを定義できます。

```php
$r->controller('UserController')->group(function (RouteCollector $r) {
    // このブロックのルート定義ではコントローラーに UserController が適用される

    $r->get()->path('/users')->action('index');
    $r->post()->path('/users')->action('store');

    $r->path('/user/{id}')->group(function (RouteCollector $r) {
        // このブロックのルート定義ではパスの先頭に /user/{id} が追加される

        // /user/{id}
        $r->get()->action('show');

        // /user/{id}/edit
        $r->get()->path('/edit')->action('edit');
    });
});
```

## マジックメソッドの警告を抑止

マジックメソッドを使用すると PhpStorm で警告が表示されますが、`RouteCollector` を継承したクラスで `@method` アノテーションを記述すれば大丈夫です。

```php
// MyRouteCollector.php

/**
 * @method $this controller(string $controller)
 * @method $this action(string $action)
 */
class MyRouteCollector extends RouteCollector {}
```

```php
// routes.php

$registry = new RouteRegistry();
$r = new MyRouteCollector($registry);
```

本来であれば `$this` ではなく `static` にするべきなのですが、なぜか PhpStorm だと `@method` ときに `static` による補完が効かなかったため `$this` にしています。

気になるなら `@method` は使わずに普通にメソッド定義をしてください。これなら `static` でも補完が効きます。

```php
// MyRouteCollector.php

class ActionRouteCollector extends RouteCollector
{
    /**
     * @param string $controller
     * @return static
     */
    public function controller($controller)
    {
        return $this->param(__FUNCTION__, $controller);
    }
}
```
