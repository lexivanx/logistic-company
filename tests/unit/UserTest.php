<?php
require_once __DIR__ . '/../../classes/User.php';

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{   
    /*
    protected $dbMock; // Mock of the database connection

    protected function setUp(): void {
        parent::setUp();

        // Mock the mysqli object
        $this->dbMock = $this->getMockBuilder(mysqli::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        // Mock the statement object
        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        // Prepare method should expect a SQL query string and return the statement mock
        $this->dbMock->expects($this->any())
                        ->method('prepare')
                        ->with($this->isType('string'))
                        ->willReturn($stmtMock);

        // Setup expectations for the statement mock
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('bind_param')->willReturn(true);
        $stmtMock->method('get_result')->willReturn($this->getDummyResult());

        // Prevent the mock from being closed or killed to simulate a persistent connection
        $this->dbMock->method('close')->willReturn(true);
        $this->dbMock->method('kill')->willReturn(true);
        $this->dbMock->method('rollback')->willReturn(true);
    }
    
    private function getDummyResult() {
        $resultMock = $this->getMockBuilder(mysqli_result::class)
                           ->disableOriginalConstructor()
                           ->getMock();
        $resultMock->method('fetch_assoc')->willReturn(['column' => 'value']);
        return $resultMock;
    }

    public function testUserAuthReturnsTrueWhenUserAndPassAreCorrect() {
        $username = 'testuser';
        $password = 'testpassword';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Pass a SQL string to prepare, even though it's not used due to mocking
        $stmt = $this->dbMock->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);
    
        $result = $this->getDummyResult();
        $result->method('fetch_assoc')->willReturn(['password' => $hashedPassword]);
    
        $authResult = User::userAuth($username, $password, $this->dbMock);
        $this->assertTrue($authResult);
    }
    */

}