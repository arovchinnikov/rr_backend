<?php

declare(strict_types=1);

namespace Core\Modules\Http\Enums;

enum ResponseCode: int
{
    /** 1XX status codes */
case continue = 100;
case switchingProtocols = 101;
case processing = 102;

    /** 2XX status codes */
case ok = 200;
case created = 201;
case accepted = 202;
case nonAuthoritativeInformation = 203;
case noContent = 204;
case resetContent = 205;
case partialContent = 206;
case multiStatus = 207;
case alreadyReported = 208;

    /** 3XX status codes */
case multipleChoices = 300;
case movedPermanently = 301;
case found = 302;
case seeOther = 303;
case notModified = 304;
case useProxy = 305;
case switchProxy = 306;
case temporaryRedirect = 307;

    /** 4XX status codes */
case badRequest = 400;
case unauthorized = 401;
case paymentRequired = 402;
case forbidden = 403;
case notFound = 404;
case methodNotAllowed = 405;
case notAcceptable = 406;
case proxyAuthenticationRequired = 407;
case requestTimeOut = 408;
case conflict = 409;
case gone = 410;
case lengthRequired = 411;
case preconditionFailed = 412;
case requestEntityTooLarge = 413;
case requestUriTooLarge = 414;
case unsupportedMediaType = 415;
case requestedRangeNotSatisfiable = 416;
case expectationFailed = 417;
case teapot = 418;
case unprocessableEntity = 422;
case locked = 423;
case failedDependency = 424;
case unorderedCollection = 425;
case upgradeRequired = 426;
case preconditionRequired = 428;
case tooManyRequests = 429;
case requestHeaderFieldsTooLarge = 431;
case unavailableForLegalReasons = 451;

    /** 5XX status codes */
case internalServerError = 500;
case notImplemented = 501;
case badGateway = 502;
case serviceUnavailable = 503;
case gatewayTimeOut = 504;
case httpVersionNotSupported = 505;
case variantAlsoNegotiates = 506;
case insufficientStorage = 507;
case loopDetected = 508;
case networkAuthenticationRequired = 511;

    public function message(): string
    {
        return match ($this) {
            /** 1XX status codes */
            self::continue => 'Continue',
            self::switchingProtocols => 'Switching Protocols',
            self::processing => 'Processing',

            /** 2XX status codes */
            self::ok => 'OK',
            self::created => 'Created',
            self::accepted => 'Accepted',
            self::nonAuthoritativeInformation => 'Non-Authoritative Information',
            self::noContent => 'No Content',
            self::resetContent => 'Reset Content',
            self::partialContent => 'Partial Content',
            self::multiStatus => 'Multi-status',
            self::alreadyReported => 'Already Reported',

            /** 3XX status codes */
            self::multipleChoices => 'Multiple Choices',
            self::movedPermanently => 'Moved Permanently',
            self::found => 'Found',
            self::seeOther => 'See Other',
            self::notModified => 'Not Modified',
            self::useProxy => 'Use Proxy',
            self::switchProxy => 'Switch Proxy',
            self::temporaryRedirect => 'Temporary Redirect',

            /** 4XX status codes */
            self::badRequest => 'Bad Request',
            self::unauthorized => 'Unauthorized',
            self::paymentRequired => 'Payment Required',
            self::forbidden => 'Forbidden',
            self::notFound => 'Not Found',
            self::methodNotAllowed => 'Method Not Allowed',
            self::notAcceptable => 'Not Acceptable',
            self::proxyAuthenticationRequired => 'Proxy Authentication Required',
            self::requestTimeOut => 'Request Time-out',
            self::conflict => 'Conflict',
            self::gone => 'Gone',
            self::lengthRequired => 'Length Required',
            self::preconditionFailed => 'Precondition Failed',
            self::requestEntityTooLarge => 'Request Entity Too Large',
            self::requestUriTooLarge => 'Request-URI Too Large',
            self::unsupportedMediaType => 'Unsupported Media Type',
            self::requestedRangeNotSatisfiable => 'Requested range not satisfiable',
            self::expectationFailed => 'Expectation Failed',
            self::teapot => 'I\'m a teapot',
            self::unprocessableEntity => 'Unprocessable Entity',
            self::locked => 'Locked',
            self::failedDependency => 'Failed Dependency',
            self::unorderedCollection => 'Unordered Collection',
            self::upgradeRequired => 'Upgrade Required',
            self::preconditionRequired => 'Precondition Required',
            self::tooManyRequests => 'Too Many Requests',
            self::requestHeaderFieldsTooLarge => 'Request Header Fields Too Large',
            self::unavailableForLegalReasons => 'Unavailable For Legal Reasons',

            /** 5XX status codes */
            self::internalServerError => 'Internal Server Error',
            self::notImplemented => 'Not Implemented',
            self::badGateway => 'Bad Gateway',
            self::serviceUnavailable => 'Service Unavailable',
            self::gatewayTimeOut => 'Gateway Time-out',
            self::httpVersionNotSupported => 'HTTP Version not supported',
            self::variantAlsoNegotiates => 'Variant Also Negotiates',
            self::insufficientStorage => 'Insufficient Storage',
            self::loopDetected => 'Loop Detected',
            self::networkAuthenticationRequired => 'Network Authentication Required',
        };
    }
    }
