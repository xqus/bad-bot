<?php

namespace xqus\BadBot\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestRateLimitedException extends HttpException {}