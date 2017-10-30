<?php

class ServiceA {

}
class ServiceB {
    public function __construct(ServiceA $service) {}
}
class ServiceC {
    public function __construct(ServiceB $service) {}
}
class ServiceD {
    public function __construct(ServiceC $service) {}
}
class ServiceE {
    public function __construct(ServiceD $service) {}
}
class ServiceF {
    public function __construct(ServiceE $service) {}
}
class ServiceG {
    public function __construct(ServiceF $service) {}
}
class ServiceH {
    public function __construct(ServiceG $service) {}
}
class ServiceI {
    public function __construct(ServiceH $service) {}
}
class ServiceJ {
    public function __construct(ServiceI $service) {}
}
