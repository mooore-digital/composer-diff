<?php

declare(strict_types=1);

namespace Mooore\ComposerDiff;

function get_package_version(array $package): string
{
    $version = $package['version'];

    if (strpos($version, 'dev-') === 0) {
        $source_reference = substr($package['source']['reference'] ?? '', 0, 6);

        return sprintf('%s#%s', $version, $source_reference);
    }

    return $version;
}

function create_diff(array $argv): void
{
    $start_revision = $argv[1] ?? null;
    $end_revision = $argv[2] ?? null;

    if ($start_revision === null) {
        printf('No start revision given!' . PHP_EOL);
        exit(1);
    }

    $start_revision = escapeshellarg($start_revision);
    $start_lockfile_contents = shell_exec(
        sprintf('git show %s:composer.lock', $start_revision)
    );

    $end_lockfile_contents = null;
    if ($end_revision === null) {
        $end_lockfile_contents = file_get_contents('composer.lock');
    } else {
        $end_revision = escapeshellarg($end_revision);
        $end_lockfile_contents = shell_exec(
          sprintf('git show %s:composer.lock', $end_revision)
        );
    }

    if (empty($start_lockfile_contents)) {
        printf('Start revision empty' . PHP_EOL);
        exit(1);
    } elseif (empty($end_lockfile_contents)) {
        printf('End revision empty' . PHP_EOL);
        exit(1);
    }

    $start_lockfile_map = json_decode($start_lockfile_contents, true);
    $end_lockfile_map = json_decode($end_lockfile_contents, true);

    $start_packages = [];
    $end_packages = [];

    foreach ($start_lockfile_map['packages'] as $package) {
        $start_packages[$package['name']] = $package;
    }
    unset($package);

    foreach ($end_lockfile_map['packages'] as $package) {
        $end_packages[$package['name']] = $package;
    }
    unset($package);

    $start_package_names = array_keys($start_packages);
    $end_package_names = array_keys($end_packages);

    sort($start_package_names);
    sort($end_package_names);

    $added_packages = array_diff($end_package_names, $start_package_names);
    $deleted_packages = array_diff($start_package_names, $end_package_names);
    $stale_packages = array_intersect($start_package_names, $end_package_names);

    printf('Deleted packages:' . PHP_EOL);
    foreach ($deleted_packages as $package_name) {
        printf('- %s' . PHP_EOL, $package_name);
    }
    unset($package_name);

    printf('Added packages:' . PHP_EOL);
    foreach ($added_packages as $package_name) {
        printf('+ %s' . PHP_EOL, $package_name);
    }
    unset($package_name);

    printf('Upgraded packages:' . PHP_EOL);
    foreach ($stale_packages as $package_name) {
        $start_package = $start_packages[$package_name];
        $start_version = get_package_version($start_package);
        $end_package = $end_packages[$package_name];
        $end_version = get_package_version($end_package);

        if ($start_version !== $end_version) {
            printf('~ %s (%s => %s)' . PHP_EOL, $package_name, $start_version, $end_version);
        }
    }
    unset($package_name);
}
