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

use Laminas\Navigation\Page\AbstractPage;
use Laminas\Stdlib\Exception\DomainException;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\NavigationHelper\Accept\AcceptHelperInterface;
use Mimmi20\NavigationHelper\ConvertToPages\ConvertToPagesInterface;
use Mimmi20\NavigationHelper\FindFromProperty\FindFromProperty;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class FindFromPropertyTest extends TestCase
{
    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testFindWrongRelation(): void
    {
        $rel = 'ABC';

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::never())
            ->method('getRel');
        $page->expects(self::never())
            ->method('getRev');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $convertToPages = $this->getMockBuilder(ConvertToPagesInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $convertToPages->expects(self::never())
            ->method('convert');

        $helper = new FindFromProperty($acceptHelper, $convertToPages);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid relation attribute "%s", must be "rel" or "rev"',
                $rel,
            ),
        );
        $this->expectExceptionCode(0);

        $helper->find($page, $rel, '');
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testFindNoRelation(): void
    {
        $rel  = 'rel';
        $type = '';

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::once())
            ->method('getRel')
            ->with($type)
            ->willReturn(null);
        $page->expects(self::never())
            ->method('getRev');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $convertToPages = $this->getMockBuilder(ConvertToPagesInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $convertToPages->expects(self::never())
            ->method('convert');

        $helper = new FindFromProperty($acceptHelper, $convertToPages);

        self::assertSame([], $helper->find($page, $rel, $type));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testFindNoConvertedRelation(): void
    {
        $rel  = 'rel';
        $type = '';
        $uri  = 'test-uri';

        $configArray = [
            'type' => 'uri',
            'uri' => $uri,
        ];

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::once())
            ->method('getRel')
            ->with($type)
            ->willReturn($configArray);
        $page->expects(self::never())
            ->method('getRev');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $convertToPages = $this->getMockBuilder(ConvertToPagesInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $convertToPages->expects(self::once())
            ->method('convert')
            ->with($configArray)
            ->willReturn([]);

        $helper = new FindFromProperty($acceptHelper, $convertToPages);

        self::assertSame([], $helper->find($page, $rel, $type));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testFindWithConvertedRelation(): void
    {
        $rel  = 'rel';
        $type = '';

        $uri1  = 'test-uri1';
        $uri2  = 'test-uri2';
        $page1 = $this->createMock(PageInterface::class);
        $page2 = $this->createMock(PageInterface::class);

        $config1 = [
            'type' => 'uri',
            'uri' => $uri1,
        ];
        $config2 = [
            'type' => 'uri',
            'uri' => $uri2,
        ];

        $config = [$config1, $config2];

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::once())
            ->method('getRel')
            ->with($type)
            ->willReturn($config);
        $page->expects(self::never())
            ->method('getRev');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher      = self::exactly(2);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (AbstractPage | PageInterface $page, bool $recursive = true) use ($matcher, $page1, $page2): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($page1, $page),
                        default => self::assertSame($page2, $page),
                    };

                    self::assertTrue($recursive);

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );

        $convertToPages = $this->getMockBuilder(ConvertToPagesInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $convertToPages->expects(self::once())
            ->method('convert')
            ->with($config)
            ->willReturn([$page1, $page2]);

        $helper = new FindFromProperty($acceptHelper, $convertToPages);

        self::assertSame([1 => $page2], $helper->find($page, $rel, $type));
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function testFindWithConvertedRelation2(): void
    {
        $rel  = 'rev';
        $type = '';

        $uri1  = 'test-uri1';
        $uri2  = 'test-uri2';
        $page1 = $this->createMock(PageInterface::class);
        $page2 = $this->createMock(PageInterface::class);

        $config1 = [
            'type' => 'uri',
            'uri' => $uri1,
        ];
        $config2 = [
            'type' => 'uri',
            'uri' => $uri2,
        ];

        $config = [$config1, $config2];

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('isActive');
        $page->expects(self::never())
            ->method('getRel');
        $page->expects(self::once())
            ->method('getRev')
            ->with($type)
            ->willReturn($config);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $matcher      = self::exactly(2);
        $acceptHelper->expects($matcher)
            ->method('accept')
            ->willReturnCallback(
                static function (AbstractPage | PageInterface $page, bool $recursive = true) use ($matcher, $page1, $page2): bool {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame($page1, $page),
                        default => self::assertSame($page2, $page),
                    };

                    self::assertTrue($recursive);

                    return match ($matcher->numberOfInvocations()) {
                        1 => false,
                        default => true,
                    };
                },
            );

        $convertToPages = $this->getMockBuilder(ConvertToPagesInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $convertToPages->expects(self::once())
            ->method('convert')
            ->with($config)
            ->willReturn([$page1, $page2]);

        $helper = new FindFromProperty($acceptHelper, $convertToPages);

        self::assertSame([1 => $page2], $helper->find($page, $rel, $type));
    }
}
