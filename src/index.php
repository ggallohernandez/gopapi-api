<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;

use App\Controllers\CertificateController;
use App\Services\CertificateManager;
use App\Services\CertificateService;
use App\Services\ICertificateManager;
use App\Services\IDomainVerifier;
use App\Services\TxtDnsRecordVerifier;

$dotenv = new Dotenv();

try {
    $dotenv->load(__DIR__ . '/.env');
} catch (PathException $e) {
    // do nothing
}

$container = new ContainerBuilder();

$container->autowire(IDomainVerifier::class, TxtDnsRecordVerifier::class);
$container->autowire(ICertificateManager::class, CertificateManager::class)
    ->addArgument(new Reference(IDomainVerifier::class));
$container->autowire(CertificateService::class, CertificateService::class)
    ->addArgument(new Reference(IDomainVerifier::class))
    ->addArgument(new Reference(ICertificateManager::class));
$container->autowire(CertificateController::class, CertificateController::class)
    ->addArgument(new Reference(CertificateService::class));

$routes = new RouteCollection();
$routes->add('create_cert', (new Route('/certificate/new', [
    '_controller' => [$container->get(CertificateController::class), 'create']]))
    ->setMethods(['POST'])
);

$request = Request::createFromGlobals();

$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
$container->set(EventDispatcher::class, $dispatcher);

$configuration = new SdkConfiguration([
    'strategy' => SdkConfiguration::STRATEGY_API,
    'domain' => $_ENV['AUTH0_DOMAIN'],
    'audience' => explode(',', $_ENV['AUTH0_AUDIENCE']),
]);

$sdk = new Auth0($configuration);

// Oauth2 Authentication middleware
$dispatcher->addListener(KernelEvents::REQUEST, function (RequestEvent $event) use ($sdk) {
    // Gets the bearer token from the Authorization header, then it decodes and validates issuer, audience and domain.
    $token = $sdk->getBearerToken(
        null, 
        null,
        ['Authorization']
    );

    if (is_null($token)) {
        $event->setResponse(new Response('Unauthorized', 401));
    }
});

$dispatcher->addListener(KernelEvents::EXCEPTION, function (ExceptionEvent $event) use ($sdk) {
    // todo Log or send to Sentry
    
    if (array_key_exists('APP_ENV', $_ENV) && $_ENV['APP_ENV'] !== 'development') {
        $event->setResponse(new Response('Internal Server Error', 500));
    }
});

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
