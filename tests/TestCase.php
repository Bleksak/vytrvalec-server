<?php

declare(strict_types=1);

namespace Tests;

use App\DataFixtures\ActivityFixtures;
use App\DataFixtures\CharityFixtures;
use App\DataFixtures\ExtraPointsFixtures;
use App\DataFixtures\FacultyFixtures;
use App\DataFixtures\SeasonFixtures;
use App\Entity\Faculty;
use App\Entity\User;
use App\Kernel;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TestCase extends BaseTestCase
{
    public function app(): Kernel
    {
        static $kernel;
        $kernel ??= (function (): Kernel {
            $env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
            $debug = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

            $kernel = new Kernel((string) $env, (bool) $debug);
            $kernel->boot();

            return $kernel;
        })();

        return $kernel;
    }

    /**
     * Shortcut to the test container (all services are public).
     */
    public function container(): ContainerInterface
    {
        $container = $this->app()->getContainer();

        return $container->has('test.service_container') ? $container->get('test.service_container') : $container;
    }

    public function createBrowser(): Client
    {
        return new Client();
    }

    public function adminToken(): string
    {
        /** @var UserRepository */
        $repository = $this->repository(User::class);

        $hasher = $this->container()->get('security.user_password_hasher');

        $user = new User(
            'admin@test.com',
            'admin',
            'admin',
            $this->repository(Faculty::class)->find(1),
            true,
            ['ROLE_STAFF', 'ROLE_USER'],
            null,
        );

        $hashed = $hasher->hashPassword($user, 'Qwerty145#!12');
        $user->setPassword($hashed);

        if ($repository->findBy(['email' => 'admin@test.com']) === []) {
            $repository->save($user, true);
        }

        $response = $this->api()->post('user/login', [
            'json' => [
                'email' => 'admin@test.com',
                'password' => 'Qwerty145#!12',
            ],
        ]);

        return json_decode($response->getBody()->getContents())->token;
    }

    public function userToken(): string
    {
        /** @var UserRepository */
        $repository = $this->repository(User::class);

        $hasher = $this->container()->get('security.user_password_hasher');

        $user = new User(
            'user@test.com',
            'user',
            'user',
            $this->repository(Faculty::class)->find(1),
            true,
            ['ROLE_USER'],
            null,
        );

        $hashed = $hasher->hashPassword($user, 'Qwerty145#!12');
        $user->setPassword($hashed);

        if ($repository->findBy(['email' => 'user@test.com']) === []) {
            $repository->save($user, true);
        }

        $response = $this->api()->post('user/login', [
            'json' => [
                'email' => 'user@test.com',
                'password' => 'Qwerty145#!12',
            ],
        ]);

        return json_decode($response->getBody()->getContents())->token;
    }

    public function api(?string $token = null): Client
    {
        $parameterBag = $this->container()->get('parameter_bag');
        $baseUri = $parameterBag->get('app_url') ?? '';

        if ($baseUri !== '') {
            $baseUri .= '/';
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X_ENV_OVERRIDE' => 'test',
        ];

        if ($token !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $token);
        }

        return new Client([
            'base_uri' => $baseUri,
            'headers' => $headers,
        ]);
    }

    /**
     * Create database if not exists.
     */
    public function createDatabase(): void
    {
        $doctrine = $this->container()->get('doctrine');
        $connection = $doctrine->getConnection($doctrine->getDefaultConnectionName());
        $params = $connection->getParams();
        $name = $params['path'] ?? $params['dbname'];
        unset($params['dbname'], $params['path'], $params['url']);
        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->connect();

        if (\in_array($name, $tmpConnection->getSchemaManager()->listDatabases(), true)) {
            return;
        }

        $tmpConnection->getSchemaManager()->createDatabase(
            $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name)
        );
    }

    public function runMigrations(): void
    {
        $dependencyFactory = $this->container()->get('doctrine.migrations.dependency_factory');
        $dependencyFactory->getMetadataStorage()->ensureInitialized();
        $migratorConfiguration = new MigratorConfiguration();
        $planCalculator = $dependencyFactory->getMigrationPlanCalculator();
        $migrator = $dependencyFactory->getMigrator();
        $version = $dependencyFactory->getVersionAliasResolver()->resolveVersionAlias('latest');
        $plan = $planCalculator->getPlanUntilVersion($version);
        $migrator->migrate($plan, $migratorConfiguration);
    }

    public function runFixtures(): void
    {
        $doctrine = $this->container()->get('doctrine');
        $entityManager = $doctrine->getManager();

        $fixtures = [
            new ActivityFixtures(),
            new CharityFixtures(),
            new FacultyFixtures(),
            new SeasonFixtures(),
            new ExtraPointsFixtures(),
        ];

        $referenceRepository = new ReferenceRepository($entityManager);

        foreach ($fixtures as $fixture) {
            $fixture->setReferenceRepository($referenceRepository);
            $fixture->load($entityManager);
        }
    }

    public function dropDatabase(): void
    {
        $doctrine = $this->container()->get('doctrine');
        $connection = $doctrine->getConnection($doctrine->getDefaultConnectionName());
        $params = $connection->getParams();
        $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($params['dbname']);
        try {
            $connection->getSchemaManager()->dropDatabase($name);
        } catch (\Throwable) {
        }
    }

    public function createSchema(): void
    {
        $entityManager = $this->container()->get(EntityManagerInterface::class);
        $schemaTool = new SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($classes);
    }

    /**
     * @return string[]
     */
    public function tables(): array
    {
        $entityManager = $this->container()->get(EntityManagerInterface::class);

        $notMappedSuperClassNames = \array_filter(
            $entityManager->getConfiguration()->getMetadataDriverImpl()->getAllClassNames(),
            fn (string $class): bool => false === $entityManager->getClassMetadata($class)->isMappedSuperclass,
        );

        return \array_map(
            fn (string $class): string => $entityManager->getClassMetadata($class)->getTableName(),
            $notMappedSuperClassNames
        );
    }

    public function dropSchema(): void
    {
        $entityManager = $this->container()->get(EntityManagerInterface::class);

        $connection = $entityManager->getConnection();
        $connection->query('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($this->tables() as $table) {
            $connection->query(\sprintf('DROP TABLE `%s`;', $table));
        }
        $connection->query('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure EntityManager doesn't contain entities after clearing DB
        $entityManager->clear();
    }

    /**
     * Use this helper at the beginning of a test to truncate all tables.
     */
    public function clearDatabase(): void
    {
        $entityManager = $this->container()->get(EntityManagerInterface::class);

        $connection = $entityManager->getConnection();
        foreach ($this->tables() as $table) {
            $connection->query(\sprintf('DELETE FROM `%s`;', $table));
        }

        // Ensure EntityManager doesn't contain entities after clearing DB
        $entityManager->clear();
    }

    public function save(object ...$entities): void
    {
        $em = $this->container()->get(EntityManagerInterface::class);
        foreach ($entities as $entity) {
            $em->persist($entity);
            $em->flush();
        }
    }

    public function remove(object $entity): void
    {
        $em = $this->container()->get(EntityManagerInterface::class);
        $em->remove($entity);
        $em->flush();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return EntityRepository<T>
     */
    public function repository(string $className): EntityRepository
    {
        return $this->container()->get(EntityManagerInterface::class)->getRepository($className);
    }

    // public function jwt(User $user): string
    // {
    //     $authenticator = container()->get('debug.App\Security\JWTAuthenticator');

    //     $jwtManager = new JWTManager(
    //         container()->get('lexik_jwt_authentication.encoder'),
    //         new EventDispatcher(), // Do not dispatch creation event to avoid dependencies on Request objects
    //     );

    //     return $jwtManager->create($user);
    // }

    // public function login(?UserInterface $user, ?string $providerKey = 'main'): void
    // {
    //     $token = null !== $user ? new UsernamePasswordToken($user, null, $providerKey, $user->getRoles()) : null;
    //     container()->get('security.token_storage')->setToken($token);
    // }

    // public function logout(): void
    // {
    //     container()->get('security.token_storage')->setToken(null);
    // }

    /**
     * @param array<string> $roles
     */
    public function createUser(
        Faculty $faculty,
        string $email = 'asdf@asdf.com',
        string $password = 'password',
        array $roles = [],
        string $firstName = '',
        string $lastName = '',
    ): User {
        return new User(
            $email,
            $firstName,
            $lastName,
            $faculty,
            true,
            $roles,
            null,
        );
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
