<?php

return [
    K911\Swoole\Bridge\Symfony\Bundle\SwooleBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class         => ['dev' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class  => ['all' => true],
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => ['test' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class         => ['all' => true],
    ControlBit\Dto\Bridge\Symfony\DtoBundle::class        => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class           => ['all' => true],
];
