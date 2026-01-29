<?php

declare(strict_types=1);

namespace App\Command;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket\InternetAddress;
use Amp\Websocket\Server\AllowOriginAcceptor;
use Amp\Websocket\Server\Websocket;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Security\AccessTokenHandler;
use App\Services\ImagePath;
use App\Websocket\SubmissionProducer\SubmissionProducerClientHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function Amp\trapSignal;

#[AsCommand(
    name: 'mv:ws-submission-producer',
    description: 'Produces submissions for websocket consumer',
)]
final readonly class WebsocketSubmissionProducerCommand
{
    /** @var int<0, 65535> */
    private int $port;

    public function __construct(
        private LoggerInterface $logger,
        private SubmissionRepository $submissionRepository,
        private UserRepository $userRepository,
        private AccessTokenHandler $accessTokenHandler,
        private SerializerInterface $serializer,
        private DenormalizerInterface $denormalizer,
        private ImagePath $imagePath,
        ParameterBagInterface $parameters,
    ) {
        $port = $parameters->get('ws_port');
        $port = (int) $port;

        \assert($port >= 0, 'Port must be >= 0');
        \assert($port <= 65535, 'Port must be <= 65535');

        $this->port = $port;
    }

    public function __invoke(): int
    {
        $server = SocketHttpServer::createForDirectAccess($this->logger);
        $server->expose(new InternetAddress('0.0.0.0', $this->port));
        $server->expose(new InternetAddress('::', $this->port));

        $errorHandler = new DefaultErrorHandler();

        $acceptor = new AllowOriginAcceptor([
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'http://[::1]:3000',
            'https://vytrvalec.uts.zcu.cz',
        ]);

        $clientHandler = new SubmissionProducerClientHandler(
            $this->submissionRepository,
            $this->userRepository,
            $this->accessTokenHandler,
            $this->serializer,
            $this->denormalizer,
            $this->imagePath,
        );

        $websocket = new Websocket(
            $server,
            $this->logger,
            $acceptor,
            $clientHandler,
        );

        $router = new Router($server, $this->logger, $errorHandler);
        $router->addRoute('GET', '/ws', $websocket);
        $router->setFallback($websocket);

        $server->start($router, $errorHandler);

        $signal = trapSignal([SIGINT, SIGTERM]);

        $this->logger->info(\sprintf(
            'Received signald %d, stopping HTTP server',
            $signal,
        ));

        $server->stop();

        return Command::SUCCESS;
    }
}
