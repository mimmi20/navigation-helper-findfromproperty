<?php
/**
 * This file is part of the mimmi20/navigation-helper-findfromproperty package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\FindFromProperty;

use Laminas\Navigation\Page\AbstractPage;
use Laminas\Stdlib\Exception\DomainException;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;

interface FindFromPropertyInterface
{
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
    public function find(AbstractPage | PageInterface $page, string $rel, string $type): array;
}
