<?php

use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testPluginVersionConstantIsDefined(): void
    {
        $this->assertSame( '2.0.2', WPO_VERSION );
    }

    public function testPluginDirectoryConstantPointsToPluginRoot(): void
    {
        $this->assertNotEmpty( WPO_PLUGIN_DIR );
        $this->assertFileExists( WPO_PLUGIN_DIR . 'wp-page-optimizer.php' );
    }
}
