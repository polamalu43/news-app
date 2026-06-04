<?php
use Core\Container;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

Container::bind(UserRepositoryInterface::class, function() {
    return new UserRepository();
});

?>
