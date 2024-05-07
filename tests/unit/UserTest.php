<?php
require_once __DIR__ . '/../../classes/User.php';

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $db; // Mock of the database connection

    protected function setUp(): void {
        parent::setUp();
        $this->db = $this->getMockBuilder(mysqli::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        // Additional safety to ensure no methods can close or disrupt the mock unexpectedly
        $this->db->method('close')->willReturn(true);
        $this->db->method('kill')->willReturn(true);
        $this->db->method('rollback')->willReturn(true);
    }

    public function testGetRole() {
        $userId = 1;
        $expectedRole = 'admin';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['role_name' => $expectedRole]);

        $role = User::getRole($userId, $this->db);
        $this->assertEquals($expectedRole, $role);
    }

    public function testGetUserShipmentErrs() {
        $sender_name = "Yanko Yanev";
        $recipient_name = "Petar Petrov";
        $delivery_name = "Ivan Ivanov";

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);

        // Simulate different fetch results for each call
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(['user_id' => 1], null, ['user_id' => 3]);

        $errors = User::getUserShipmentErrs($sender_name, $recipient_name, $delivery_name, $this->db);

        $this->assertCount(1, $errors);
        $this->assertContains("Recipient not registered! Please leave blank", $errors);
    }

    public function testFetchCustomers() {
        $expectedResult = [['id' => 1, 'username' => 'yankoyanev', 'full_name' => 'Yanko Yanev']];
        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $customers = User::fetchCustomers($this->db);
        $this->assertEquals($expectedResult, $customers);
    }

    public function testFetchEmployees() {
        $expectedResult = [['id' => 2, 'username' => 'yankoyanev', 'office_id' => 101, 'full_name' => 'Yanko Yanev']];
        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $employees = User::fetchEmployees($this->db);
        $this->assertEquals($expectedResult, $employees);
    }
}
