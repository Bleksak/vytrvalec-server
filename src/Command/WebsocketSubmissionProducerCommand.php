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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function Amp\trapSignal;

#[AsCommand(
    name: 'mv:ws-submission-producer',
    description: 'Produces submissions for websocket consumer',
)]
final class WebsocketSubmissionProducerCommand extends Command
{
    private readonly int $port;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SubmissionRepository $submissionRepository,
        private readonly UserRepository $userRepository,
        private readonly AccessTokenHandler $accessTokenHandler,
        private readonly SerializerInterface $serializer,
        private readonly DenormalizerInterface $denormalizer,
        private readonly ImagePath $imagePath,
        ParameterBagInterface $parameters,
    ) {
        parent::__construct();

        $port = $parameters->get('ws_port');
        $port = (int) $port;
        assert($port >= 0, 'Port must be greater than 0');
        assert($port <= 65535, 'Port must be less than or equal to 65535');

        $this->port = $port;
    }

    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $server = SocketHttpServer::createForDirectAccess($this->logger);
        $server->expose(new InternetAddress('127.0.0.1', $this->port));
        $server->expose(new InternetAddress('[::1]', $this->port));

        $errorHandler = new DefaultErrorHandler();

        $acceptor = new AllowOriginAcceptor(
            ['http://localhost:3000', 'http://127.0.0.1:3000', 'http://[::1]/:3000']
        );

        $clientHandler = new SubmissionProducerClientHandler(
            $this->submissionRepository,
            $this->userRepository,
            $this->accessTokenHandler,
            $this->serializer,
            $this->denormalizer,
            $this->imagePath,
        );

        $websocket = new Websocket($server, $this->logger, $acceptor, $clientHandler);

        $router = new Router($server, $this->logger, $errorHandler);
        $router->addRoute('GET', '/ws', $websocket);
        $router->setFallback($websocket);

        $server->start($router, $errorHandler);

        $signal = trapSignal([SIGINT, SIGTERM]);

        $this->logger->info(sprintf('Received signald %d, stopping HTTP server', $signal));

        $server->stop();

        return Command::SUCCESS;
    }
}
