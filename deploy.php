<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

// Config

set('bin/php', function () {
    return '/usr/bin/php';
});

set('application', 'booking.thaiquran.com');
set('http_user', 'www-data');
set('repository', 'https://github.com/arifsetianto/booking-app.git');

set('git_tty', true);
set('git_ssh_command', 'ssh -o StrictHostKeyChecking=no');

set('keep_releases', 5);

set('writable_mode', 'chmod');
set('writable_chmod_mode', '777');

add('shared_files', ['.env']);
add('shared_dirs', ['storage']);
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// Hosts

host('prod')
    ->setHostname(getenv('HOST'))
    ->set('remote_user', getenv('USERNAME'))
    ->set('port', getenv('PORT'))
    ->set('branch', 'main')
    ->set('deploy_path', '/var/www/{{application}}');

// Hooks

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});

desc('Build assets');
task('deploy:build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

task('deploy', [
    'deploy:prepare',
    'deploy:secrets',
    'deploy:vendors',
    'deploy:shared',
    'artisan:storage:link',
    'deploy:publish',
    'deploy:unlock',
]);

after('deploy:update_code', 'deploy:build');
after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');
after('artisan:migrate', 'artisan:horizon:terminate');
