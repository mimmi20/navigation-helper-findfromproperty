<?php

/**
 * This file is part of the mimmi20/navigation-helper-findfromproperty package.
 *
 * Copyright (c) 2021-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\FindFromProperty;

use Laminas\Navigation\AbstractContainer;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\Stdlib\Exception\DomainException;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\Navigation\ContainerInterface;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;
use Mimmi20\NavigationHelper\Accept\AcceptHelperInterface;
use Mimmi20\NavigationHelper\ConvertToPages\ConvertToPagesInterface;
use Override;

use function array_filter;
use function assert;
use function get_debug_type;
use function is_iterable;
use function is_string;
use function sprintf;

final readonly class FindFromProperty implements FindFromPropertyInterface
{
    /** @throws void */
    public function __construct(
        private AcceptHelperInterface $acceptHelper,
        private ConvertToPagesInterface $convertToPages,
    ) {
        // nothing to do
    }

    /**
     * Finds relations of given $type for $page by checking if the
     * relation is specified as a property of $page
     *
     * @param AbstractPage|PageInterface $page page to find relations for
     * @param string                     $rel  relation, 'rel' or 'rev'
     * @param string                     $type link type, e.g. 'start', 'next'
     *
     * @return array<(AbstractPage|PageInterface)>
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    #[Override]
    public function find(AbstractPage | PageInterface $page, string $rel, string $type): array
    {
        $result = match ($rel) {
            'rel' => $page->getRel($type),
            'rev' => $page->getRev($type),
            default => throw new DomainException(
                sprintf(
                    'Invalid relation attribute "%s", must be "rel" or "rev"',
                    $rel,
                ),
            ),
        };

        if (!$result) {
            return [];
        }

        assert(
            is_iterable($result)
            || is_string($result)
            || $result instanceof AbstractContainer
            || $result instanceof ContainerInterface,
            get_debug_type($result),
        );

        $result = $this->convertToPages->convert($result);

        if ($result === []) {
            return [];
        }

        return array_filter(
            $result,
            /** @throws void */
            fn (AbstractPage | PageInterface $page): bool => $this->acceptHelper->accept($page),
        );
    }
}
