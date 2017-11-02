<?php

use Krak\Cargo\{
    Container,
    Container\BoxContainer,
    Container\LoggingContainer,
    ServiceProvider,
    Test\Fixtures\CaptureLogger
};
use Psr\Log\{LoggerInterface, LoggerTrait, LogLevel};

describe('Logging Container', function() {
    it('decorates and logs the core API', function() {
        $c = new BoxContainer();
        $logger = new CaptureLogger();
        $c = new LoggingContainer($c, $logger, LogLevel::INFO);
        $c->add('a', 1);
        expect(count($logger->logs))->to->equal(1);
        $c->get('a');
        expect(count($logger->logs))->to->equal(2);
        $c->remove('a');
        expect(count($logger->logs))->to->equal(3);
        $c->register(new class() implements ServiceProvider {
            public function register(Container $c) {

            }
        });
        expect(count($logger->logs))->to->equal(4);
        expect($logger->logs[0][0])->to->equal(LogLevel::INFO);
        expect($logger->logs[1][1])->to->equal('Cargo Container making service {id}');
    });
});
