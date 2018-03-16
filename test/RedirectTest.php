<?php

namespace Amp\Http\Server\Test;

use Amp\Http\Server\Request;
use Amp\Http\Status;
use League\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface as PsrUri;
use function Amp\Http\Server\redirect;
use function Amp\Promise\wait;

class RedirectTest extends TestCase {
    /**
     * @expectedException \Error
     * @expectedExceptionMessage Invalid redirect URI; Host redirect must not contain a query or fragment component
     */
    public function testBadRedirectPath() {
        redirect(Uri\Http::createFromString("http://localhost/?foo"));
    }

    /**
     * @expectedException \Error
     * @expectedExceptionMessage Invalid redirect code; code in the range 300..399 required
     */
    public function testBadRedirectCode() {
        redirect(Uri\Http::createFromString("http://localhost"), Status::CREATED);
    }

    public function testSuccessfulAbsoluteRedirect() {
        $action = redirect(Uri\Http::createFromString("https://localhost"), Status::MOVED_PERMANENTLY);
        $request = new class extends Request {
            public function __construct() {
            }

            public function getUri(): PsrUri {
                return Uri\Http::createFromString("http://test.local/foo");
            }
        };

        /** @var \Amp\Http\Server\Response $response */
        $response = wait($action->handleRequest($request));

        $this->assertSame(Status::MOVED_PERMANENTLY, $response->getStatus());
        $this->assertSame("https://localhost/foo", $response->getHeader("location"));
    }

    public function testSuccessfulRelativeRedirect() {
        $action = redirect(Uri\Http::createFromString("/test"));
        $request = new class extends Request {
            public function __construct() {
            }

            public function getUri(): PsrUri {
                return Uri\Http::createFromString("http://test.local/foo");
            }
        };

        /** @var \Amp\Http\Server\Response $response */
        $response = wait($action->handleRequest($request));

        $this->assertSame(Status::TEMPORARY_REDIRECT, $response->getStatus());
        $this->assertSame("/test/foo", $response->getHeader("location"));
    }
}
