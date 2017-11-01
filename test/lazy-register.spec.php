<?php

use Krak\Cargo\{
    Container,
    Container\ContainerDecorator,
    Container\BoxContainer,
    Container\LazyRegisterContainer,
    Container\LazyRegister\BuildLazyRegister,
    Container\LazyRegister\BuildLazyRegisterContainer,
    Exception\LazyRegisterException,
    ServiceProvider
};

// provider a has no dependencies
class ServiceProviderA implements ServiceProvider {
    public function register(Container $c) {
        $c->add('a', 1);
        $c->add('b', 2);
    }
}

// provider b depends on a. If b needs to be loaded, a needs to load first
class ServiceProviderB implements ServiceProvider {
    public function register(Container $c) {
        $c->add('b', 3);
        $c->add('c', 4);
    }
}

// provider c depends on d (for service b)
class ServiceProviderC implements ServiceProvider {
    public function register(Container $c) {
        $c->add('a', 1);
        $c->register(new ServiceProviderD());
        $c->add('b', 1);
    }
}

// provider d depends on c (for service a)
class ServiceProviderD implements ServiceProvider {
    public function register(Container $c) {
        $c->add('a', 2);
        $c->add('b', 2);
    }
}

describe('Lazy Register Builder', function() {
    it('can build export lazy config', function() {
        $build = new BuildLazyRegister('mypath.php', function($path, $contents) {
            assert($path == 'mypath.php');
            assert(strpos($contents, "<?php\n\nreturn ") === 0);
        });
        $c = new BoxContainer();
        $build->buildLazyRegister($c, function($c) {
            $c->register(new ServiceProviderA());
        });
    });
    it('can generate lazy config with simple providers', function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        $c->register(new ServiceProviderA());
        $config = $c->exportLazyConfig();

        expect($config)->equal([
            'providers' => [
                'ServiceProviderA' => [],
            ],
            'services' => [
                'a' => 'ServiceProviderA',
                'b' => 'ServiceProviderA',
            ]
        ]);
    });
    it('can generate lazy config with providers with dependencies', function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        $c->register(new ServiceProviderA());
        $c->register(new ServiceProviderB());
        $config = $c->exportLazyConfig();

        expect($config)->equal([
            'providers' => [
                'ServiceProviderA' => [],
                'ServiceProviderB' => ['ServiceProviderA']
            ],
            'services' => [
                'a' => 'ServiceProviderA',
                'b' => 'ServiceProviderA',
                'c' => 'ServiceProviderB',
            ]
        ]);
    });
    it('can service providers with a constructor', function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        expect(function() use ($c) {
            $c->register(new class implements ServiceProvider {
                public function __construct() {}
                public function register(Container $c) {}
            });
        })->to->throw(LazyRegisterException::class);
    });
    it('prevents any circular dependencies', function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        expect(function() use ($c) {
            $c->register(new ServiceProviderC());
        })->to->throw(LazyRegisterException::class, 'Service b introduced a circular dependency: ServiceProviderC -> ServiceProviderD -> ServiceProviderC');
    });
    it('does not create a circular dependency if current provider extends itself', function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        expect(function() use ($c) {
            $c->register(new class implements ServiceProvider {
                public function register(Container $c) {
                    $c->add('a', 1);
                    $c->add('a', 2);
                }
            });
        })->to->not->throw(LazyRegisterException::class);
    });
});
describe('Lazy Register Container', function() {
    beforeEach(function() {
        $c = new BoxContainer();
        $c = new BuildLazyRegisterContainer($c);

        $c->register(new ServiceProviderA());
        $c->register(new ServiceProviderB());
        $lazy_config = $c->exportLazyConfig();

        $this->registered = [];
        $c = new BoxContainer();
        $c = new class($c, $this->registered) extends ContainerDecorator {
            public function __construct(Container $c, &$registered) {
                parent::__construct($c);
                $this->registered = &$registered;
            }
            public function register(ServiceProvider $provider, Container $c = null) {
                $this->registered[] = get_class($provider);
                $this->container->register($provider, $c ?: $this);
            }
        };
        $this->c = new LazyRegisterContainer($c, $lazy_config);
    });
    it('lazy loads services', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        expect($this->registered)->to->equal([]);
    });
    it('loads a service provider when a service is called', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        $this->c->get('a');
        expect($this->registered)->to->be->loosely->equal(['ServiceProviderA']);
    });
    it('loads dependent service providers first when a service is called', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        $this->c->get('c');
        expect($this->registered)->to->be->loosely->equal(['ServiceProviderA', 'ServiceProviderB']);
    });
    it('checks the lazy config to see if a service exists', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        expect($this->c->has('c'))->to->be->ok;
        expect($this->registered)->to->be->equal([]);
    });
    it('does not register a lazy service twice', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        $this->c->get('a');
        $this->c->get('c');
        expect($this->registered)->to->be->equal(['ServiceProviderA', 'ServiceProviderB']);
    });
    it('loads keys from the lazy config and inner containers', function() {
        $this->c->register(new ServiceProviderA());
        $this->c->register(new ServiceProviderB());
        $this->c->get('a');
        expect($this->c->keys())->to->equal(['a', 'b', 'c']);
    });
});
