<?php

declare(strict_types=1);

use App\Application\Session\Session;
use App\Application\TwigExtension\UserExtension;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use Twig\Cache\CacheInterface;

class TwigCache implements CacheInterface
{
    public function __construct(
        private Symfony\Contracts\Cache\CacheInterface $cache,
    ) {
    }

    public function generateKey(string $name, string $className): string
    {
        return sprintf('twig-%s', sha1($name . $className));
    }

    public function write(string $key, string $content): void
    {
        $this->cache->delete($key);
        $this->cache->delete(sprintf('%s-ts', $key));

        $this->cache->get($key, fn() => $content);
        $this->cache->get(sprintf('%s-ts', $key), fn() => time());
    }

    public function load(string $key): void
    {
        $content = $this->cache->get($key, fn() => null);

        if ($content !== null) {
            eval('?>' . $content);
        }
    }

    public function getTimestamp(string $key): int
    {
        return (int)$this->cache->get(sprintf('%s-ts', $key), fn() => 0);
    }
}

return function (App $app): void {
    $cache = $app->getContainer()->get('cache');

    $twigCache = new TwigCache($cache);

    $twig = Twig::create(
        __DIR__ . '/../src/' . $_ENV['PROJECT_NAME'] . '/Templates',
        [
            'cache' => $twigCache,
            'debug' => $_ENV['DEBUG'] === 'true',
            'auto_reload' => true,
        ],
    );

    $twig->addExtension(new UserExtension(
        $app->getContainer()->get(Session::class),
    ));

    $app->add(TwigMiddleware::create($app, $twig));
};
