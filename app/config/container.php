<?php

declare(strict_types=1);

use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use DI\FactoryInterface;
use Psr\Container\ContainerInterface;
use yii\caching\CacheInterface;
use yii\di\Container;
use yii\mail\MailerInterface;
use yii\rbac\ManagerInterface;
use kuaukutsu\poc\demo\components\container\ContainerDecorator;
use kuaukutsu\poc\demo\components\security\SecurityDecorator;
use kuaukutsu\poc\demo\components\security\SecurityInterface;

$container = [
    'resolveArrays' => true,
    'singletons' => [
        Container::class => static fn() => Yii::$container,
        CacheInterface::class => static fn() => Yii::$app->getCache(),
        ManagerInterface::class => static fn() => Yii::$app->getAuthManager(),
        MailerInterface::class => static fn() => Yii::$app->getMailer(),
        ContainerInterface::class => new ContainerDecorator(),
        FactoryInterface::class => new ContainerDecorator(),
        ConsoleOutputInterface::class => ConsoleOutput::class,
        SecurityInterface::class => SecurityDecorator::class,
        UuidFactoryInterface::class => UuidFactory::class,
    ],
];

return $container;
