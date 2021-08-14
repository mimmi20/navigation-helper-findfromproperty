<?php
/**
 * This file is part of the mimmi20/navigation-helper-findfromproperty package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\FindFromProperty;

use Laminas\Navigation\Page\AbstractPage;
use Laminas\Stdlib\Exception\DomainException;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Mezzio\Navigation\Page\PageInterface;
use Mimmi20\NavigationHelper\Accept\AcceptHelperInterface;
use Mimmi20\NavigationHelper\ConvertToPages\ConvertToPagesInterface;

use function array_filter;
use function sprintf;

final class FindFromProperty implements FindFromPropertyInterface
{
    private AcceptHelperInterface $acceptHelper;

    private ConvertToPagesInterface $convertToPages;

    public function __construct(AcceptHelperInterface $acceptHelper, ConvertToPagesInterface $convertToPages)
    {
        $this->acceptHelper   = $acceptHelper;
        $this->convertToPages = $convertToPages;
    }

    /**
     * Finds relations of given $type for $page by checking if the
     * relation is specified as a property of $page
     *
     * @param AbstractPage|PageInterface $page page to find relations for
     * @param string                     $rel  relation, 'rel' or 'rev'
     * @param string                     $type link type, e.g. 'start', 'next'
     *
     * @return array<(PageInterface|AbstractPage)>
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function find($page, string $rel, string $type): array
    {
        switch ($rel) {
            case 'rel':
                $result = $page->getRel($type);
                break;
            case 'rev':
                $result = $page->getRev($type);
                break;
            default:
                throw new DomainException(
                    sprintf(
                        'Invalid relation attribute "%s", must be "rel" or "rev"',
                        $rel
                    )
                );
        }

        if (!$result) {
            return [];
        }

        $result = $this->convertToPages->convert($result);

        if ([] === $result) {
            return [];
        }

        return array_filter(
            $result,
            /*
             * @param PageInterface|AbstractPage $page
             */
            fn ($page): bool => $this->acceptHelper->accept($page)
        );
    }
}
