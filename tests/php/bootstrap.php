<?php

declare(strict_types=1);

/**
 * Bootstrap für PHPUnit-Tests des BS_Modular-Framework Plugins.
 */

// Versuche, WordPress-Root zu finden, ausgehend vom Plugin-Ordner.
$plugin_dir = dirname(__DIR__, 2);
$wp_root    = dirname(dirname($plugin_dir));

// Falls eine lokale wp-tests-config vorhanden ist, könnte diese hier eingebunden werden.
// Für diesen MVP-Smoke-Test prüfen wir nur grundlegende Bootstrap-Funktionalität.

require $plugin_dir . '/bs-modular-framework.php';

