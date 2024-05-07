<?php
require_once '../../classes/User.php';

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $db; // Mock of the database connection

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for the mysqli connection
        $this->db = $this->createMock(mysqli::class);
    }

    public function testGetRole()
    {
        $userId = 1;
        $expectedRole = 'admin';

        // Create a statement mock
        $stmt = $this->createMock(mysqli_stmt::class);

        // Set up expectations for the database connection
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Set up expectations for the statement
        $stmt->expects($this->once())
            ->method('bind_param')
            ->with('i', $userId);

        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->expects($this->once())
            ->method('get_result')
            ->willReturn($result);

        $result->expects($this->once())
            ->method('fetch_assoc')
            ->willReturn(['role_name' => $expectedRole]);

        // Invoke the method
        $role = User::getRole($userId, $this->db);

        // Assert the expected role is returned
        $this->assertEquals($expectedRole, $role);
    }

    public function testGetUserShipmentErrs() {
        $sender_name = "Yanko Yanev";
        $recipient_name = "Petar Petrov";
        $delivery_name = "Ivan Ivanov";

        // Mocking getUserIdByFullName to simulate database responses
        $this->db->method('prepare')->willReturn($stmt = $this->createMock(mysqli_stmt::class));
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(['user_id' => 1], null, ['user_id' => 3]);

        // Expectations for errors based on inputs and simulated database responses
        $errors = User::getUserShipmentErrs($sender_name, $recipient_name, $delivery_name, $this->db);
        
        $this->assertCount(1, $errors);  // Only one error expected: recipient not registered
        $this->assertContains("Recipient not registered! Please leave blank", $errors);
    }


    public function testFetchCustomers() {
        // Set up the method response
        $expectedResult = [
            ['id' => 1, 'username' => 'yankoyanev', 'full_name' => 'Yanko Yanev']
        ];

        // Mocking database interactions
        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $customers = User::fetchCustomers($this->db);

        $this->assertEquals($expectedResult, $customers);
    }

    public function testFetchEmployees() {
        // Set up the method response
        $expectedResult = [
            ['id' => 2, 'username' => 'yankoyanev', 'office_id' => 101, 'full_name' => 'Yanko Yanev']
        ];

        // Mocking database interactions
        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $employees = User::fetchEmployees($this->db);

        $this->assertEquals($expectedResult, $employees);
    }



}

?>