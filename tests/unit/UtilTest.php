<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/authentication.php';
require_once __DIR__ . '/../../includes/http.php';
require_once __DIR__ . '/../../includes/queries.php';

class UtilTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        if (!session_id()) {
            session_start();
        }
    }

    protected function tearDown(): void {
        session_destroy();
        parent::tearDown();
    }

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


    public function testRedirectToPath() {
        $this->expectOutputRegex('/Headers already sent/');

        // Simulate that headers are already sent
        echo 'Some output';
        redirectToPath("/new-path");

        // Clear the output buffer and test redirection
        ob_end_clean();

        // We use headers_list() to inspect the headers and expect redirection
        redirectToPath("/new-path");
        $headers = headers_list();
        $this->assertContains("Location: http://localhost/new-path", $headers);
    }

    public function testGetQueryType() {
        // Test allowed values
        $this->assertSame('all', getQueryType('all'));
        $this->assertSame('by_employee', getQueryType('by_employee'));

        // Test default behavior with an invalid value
        $this->assertSame('all', getQueryType('invalid_query_type'));
    }

}
