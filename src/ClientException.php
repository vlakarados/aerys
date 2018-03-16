<?php

namespace Amp\Http\Server;

/**
 * A ClientException thrown from Body::read() or Body::buffer() indicates that the requesting client stream has been
 * closed, likely due to exceeding the body size limit.
 *
 * Applications may optionally catch this exception in request handlers to continue other processing. Users are NOT
 * required to catch it and if left uncaught it will simply end request handler execution. For streaming response bodies
 * in which the handler is also reading the request body, this exception should be caught and used to fail the streaming
 * response body.
 *
 * Creating and throwing a ClientException from a request handler or failing streaming response body will abruptly
 * disconnect a client.
 *
 * Responses returned by request handlers after a ClientException has been thrown will be ignored, as a response has
 * already been generated by the error handler and the client disconnected.
 */
class ClientException extends \Exception {
}
