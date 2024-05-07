<?php
require_once __DIR__ . '/../../classes/User.php';

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $dbMock; // Mock of the database connection

    protected function setUp(): void {
        parent::setUp();
    
        // Mock the mysqli object
        $dbMock = $this->getMockBuilder(mysqli::class)
                       ->disableOriginalConstructor()
                       ->getMock();
    
        // Mock the statement object
        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                         ->disableOriginalConstructor()
                         ->getMock();
    
        // Configure the database mock to always return the statement mock when prepare is called
        $dbMock->method('prepare')->willReturn($stmtMock);
    
        // Prevent the mock from being closed or killed to simulate a persistent connection
        $dbMock->method('close')->willReturn(true);
        $dbMock->method('kill')->willReturn(true);
        $dbMock->method('rollback')->willReturn(true);
    
        // Setup expectations for the statement mock
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('bind_param')->willReturn(true);
        $stmtMock->method('get_result')->willReturn($this->getDummyResult());
    
        // Assign the mock as a class attribute if needed throughout the tests
        $this->dbMock = $dbMock;
    }

    private function getDummyResult() {
        // Create a mock for mysqli_result
        $resultMock = $this->getMockBuilder(mysqli_result::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $resultMock->method('fetch_assoc')->willReturn(['column' => 'value']);
        return $resultMock;
    }

    // Example test method
    public function testUserAuthReturnsTrueWhenUserAndPassAreCorrect() {
        $username = 'testuser';
        $password = 'testpassword';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Using dbMock prepared in setUp()
        $stmt = $this->dbMock->prepare();
        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);

        $result = $this->getDummyResult();
        $result->method('fetch_assoc')->willReturn(['password' => $hashedPassword]);

        $authResult = User::userAuth($username, $password, $this->dbMock);
        $this->assertTrue($authResult);
    }

}