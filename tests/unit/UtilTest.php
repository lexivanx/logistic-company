<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/authentication.php';
require_once __DIR__ . '/../../includes/http.php';
require_once __DIR__ . '/../../includes/queries.php';

class UtilTest extends TestCase
{   
    private $headers = [];
    private $output = '';

    protected function setUp(): void {
        ob_start();
        session_start();
        parent::setUp();
    }

    protected function tearDown(): void {
        session_write_close();
        ob_end_clean();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     */
    public function testCheckAuthentication() {
        // Ensure it returns false when the session variable is not set
        $this->assertFalse(checkAuthentication());

        // Set session variable and test for true
        $_SESSION['is_logged_in'] = true;
        $this->assertTrue(checkAuthentication());

        // Test for false when the session variable is explicitly false
        $_SESSION['is_logged_in'] = false;
        $this->assertFalse(checkAuthentication());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetQueryType() {
        // Test allowed values
        $this->assertSame('all', getQueryType('all'));
        $this->assertSame('by_employee', getQueryType('by_employee'));

        // Test default behavior with an invalid value
        $this->assertSame('all', getQueryType('invalid_query_type'));
    }


    /*
    public function testRedirectToPath() {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTPS'] = 'off';

        try {
            redirectToPath("/new-path");
        } catch (Exception $e) {
            
        }

        $output = ob_get_contents();

        // Simulate header function
        $this->headers[] = "Location: http://" . $_SERVER['HTTP_HOST'] . "/new-path";

        $this->assertNotEmpty($this->headers_list());
        $this->assertContains("Location: http://localhost/new-path", $this->headers_list());
        $this->assertStringContainsString("Headers already sent", $output);
    }
    */

}
