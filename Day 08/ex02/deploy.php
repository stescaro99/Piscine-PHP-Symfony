<?php
namespace Deployer;

require 'recipe/symfony.php';

set('repository', 'https://github.com/symfony/symfony-standard.git');
set('branch', 'master');
set('deploy_path', '/var/www/production');

host('production')
    ->hostname('default') // Puoi mettere l'IP della VM, es: 192.168.33.10
    ->user('vagrant')
    ->identityFile('~/.vagrant.d/insecure_private_key')
    ->forwardAgent(true);

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
]);

task('update:hash:version', function () {
    $hash = run('cd {{release_path}} && git rev-parse HEAD');
    run("echo '$hash' > {{release_path}}/VERSION.txt");
});

after('deploy:publish', 'update:hash:version');