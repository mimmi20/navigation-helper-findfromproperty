<?php
/**
 * This file is part of the mimmi20/navigation-helper-findfromproperty package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\NavigationHelper\FindFromProperty;

use AssertionError;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\NavigationHelper\Accept\AcceptHelperInterface;
use Mimmi20\NavigationHelper\ConvertToPages\ConvertToPagesInterface;
use Mimmi20\NavigationHelper\FindFromProperty\FindFromProperty;
use Mimmi20\NavigationHelper\FindFromProperty\FindFromPropertyFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

final class FindFromPropertyFactoryTest extends TestCase
{
    private FindFromPropertyFactory $factory;

    /** @throws void */
    protected function setUp(): void
    {
        $this->factory = new FindFromPropertyFactory();
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithoutOptions(): void
    {
        $options = [
            'authorization' => null,
            'renderInvisible' => false,
            'role' => null,
        ];

        $convertToPages = $this->createMock(ConvertToPagesInterface::class);
        $acceptHelper   = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(ConvertToPagesInterface::class)
            ->willReturn($convertToPages);
        $container->expects(self::never())
            ->method('has');
        $container->expects(self::once())
            ->method('build')
            ->with(AcceptHelperInterface::class, $options)
            ->willReturn($acceptHelper);

        $helper = ($this->factory)($container, '');

        self::assertInstanceOf(FindFromProperty::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithOptions(): void
    {
        $auth            = $this->createMock(AuthorizationInterface::class);
        $renderInvisible = true;
        $role            = 'test-role';

        $options = [
            'authorization' => $auth,
            'renderInvisible' => $renderInvisible,
            'role' => $role,
        ];

        $convertToPages = $this->createMock(ConvertToPagesInterface::class);
        $acceptHelper   = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(ConvertToPagesInterface::class)
            ->willReturn($convertToPages);
        $container->expects(self::never())
            ->method('has');
        $container->expects(self::once())
            ->method('build')
            ->with(AcceptHelperInterface::class, $options)
            ->willReturn($acceptHelper);

        $helper = ($this->factory)(
            $container,
            '',
            $options,
        );

        self::assertInstanceOf(FindFromProperty::class, $helper);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testInvocationWithAssertionError(): void
    {
        $auth            = $this->createMock(AuthorizationInterface::class);
        $renderInvisible = true;
        $role            = 'test-role';

        $options = [
            'authorization' => $auth,
            'renderInvisible' => $renderInvisible,
            'role' => $role,
        ];

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::never())
            ->method('get');
        $container->expects(self::never())
            ->method('has');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($container instanceof ServiceLocatorInterface)');

        ($this->factory)(
            $container,
            '',
            $options,
        );
    }
}
