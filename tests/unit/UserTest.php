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

    public function testUserAuthReturnsTrueWhenUserAndPassAreCorrect() {
        $username = 'testuser';
        $password = 'testpassword';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['password' => $hashedPassword]);

        $authResult = User::userAuth($username, $password, $this->db);
        $this->assertTrue($authResult);
    }

    public function testUserAuthReturnsFalseWhenUserOrPassAreIncorrect() {
        $username = 'testuser';
        $password = 'testpassword';
        $hashedPassword = password_hash('wrongpassword', PASSWORD_DEFAULT);

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['password' => $hashedPassword]);

        $authResult = User::userAuth($username, $password, $this->db);
        $this->assertFalse($authResult);
    }

    public function testGetUserIdByUsernameReturnsUserIdIfExists() {
        $username = 'testuser';
        $expectedUserId = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['id' => $expectedUserId]);

        $userId = User::getUserIdByUsername($username, $this->db);
        $this->assertEquals($expectedUserId, $userId);
    }

    public function testGetUserIdByUsernameReturnsNullIfUserDoesNotExist() {
        $username = 'nonexistentuser';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($username));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $userId = User::getUserIdByUsername($username, $this->db);
        $this->assertNull($userId);
    }

    public function testGetUserFullNameByIdReturnsFullNameIfExists() {
        $userId = 1;
        $expectedFullName = 'John Doe';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['full_name' => $expectedFullName]);

        $fullName = User::getUserFullNameById($userId, $this->db);
        $this->assertEquals($expectedFullName, $fullName);
    }

    public function testGetUserFullNameByIdReturnsNullIfUserDoesNotExist() {
        $userId = 999;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $fullName = User::getUserFullNameById($userId, $this->db);
        $this->assertNull($fullName);
    }

    public function testGetUserIdByFullNameReturnsUserIdIfExists() {
        $fullName = 'John Doe';
        $expectedUserId = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($fullName));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['id' => $expectedUserId]);

        $userId = User::getUserIdByFullName($fullName, $this->db);
        $this->assertEquals($expectedUserId, $userId);
    }

    public function testGetUserIdByFullNameReturnsNullIfUserDoesNotExist() {
        $fullName = 'Nonexistent User';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('s'), $this->equalTo($fullName));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $userId = User::getUserIdByFullName($fullName, $this->db);
        $this->assertNull($userId);
    }

    public function testGetCompanyIdByUserIdReturnsCompanyIdIfExists() {
        $userId = 1;
        $expectedCompanyId = 123;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['company_id' => $expectedCompanyId]);

        $companyId = User::getCompanyIdByUserId($userId, $this->db);
        $this->assertEquals($expectedCompanyId, $companyId);
    }

    public function testGetCompanyIdByUserIdReturnsNullIfUserDoesNotExist() {
        $userId = 999;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $companyId = User::getCompanyIdByUserId($userId, $this->db);
        $this->assertNull($companyId);
    }

    public function testGetRoleReturnsRoleNameIfExists() {
        $userId = 1;
        $expectedRoleName = 'admin';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(['role_name' => $expectedRoleName]);

        $roleName = User::getRole($userId, $this->db);
        $this->assertEquals($expectedRoleName, $roleName);
    }

    public function testGetRoleReturnsNullIfRoleDoesNotExist() {
        $userId = 999;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $roleName = User::getRole($userId, $this->db);
        $this->assertNull($roleName);
    }

    public function testGetRoleReturnsNullIfQueryExecutionFails() {
        $userId = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(false);

        $roleName = User::getRole($userId, $this->db);
        $this->assertNull($roleName);
    }

    public function testGetRoleReturnsNullIfNoRoleFound() {
        $userId = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);

        $stmt->method('bind_param')->with($this->equalTo('i'), $this->equalTo($userId));
        $stmt->method('execute')->willReturn(true);

        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $roleName = User::getRole($userId, $this->db);
        $this->assertNull($roleName);
    }

    public function testGetUserShipmentErrsReturnsErrorsForInvalidAddressFields() {
        $senderName = '';
        $recipientName = 'Nonexistent User';
        $deliveryName = 'Nonexistent Driver';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);

        $errors = User::getUserShipmentErrs($senderName, $recipientName, $deliveryName, $this->db);

        $this->assertCount(3, $errors);
        $this->assertContains("Sender can't be empty!", $errors);
        $this->assertContains("Sender name does not exist!", $errors);
        $this->assertContains("No such driver exists!", $errors);
    }

    public function testGetUserShipmentErrsReturnsErrorForUnregisteredRecipient() {
        $senderName = 'John Doe';
        $recipientName = 'Nonexistent User';
        $deliveryName = '';

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $result = $this->createMock(mysqli_result::class);
        $stmt->method('get_result')->willReturn($result);
        $result->method('fetch_assoc')->willReturn(null);

        $errors = User::getUserShipmentErrs($senderName, $recipientName, $deliveryName, $this->db);

        $this->assertCount(1, $errors);
        $this->assertContains("Recipient not registered! Please leave blank", $errors);
    }

    public function testFetchCustomersReturnsArrayOfCustomers() {
        $expectedResult = [['id' => 1, 'username' => 'testuser', 'full_name' => 'John Doe']];

        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $customers = User::fetchCustomers($this->db);
        $this->assertEquals($expectedResult, $customers);
    }

    public function testFetchEmployeesReturnsArrayOfEmployees() {
        $expectedResult = [['id' => 2, 'username' => 'testuser', 'office_id' => 101, 'full_name' => 'John Doe']];

        $this->db->method('query')->willReturn($result = $this->createMock(mysqli_result::class));
        $result->method('fetch_all')->willReturn($expectedResult);

        $employees = User::fetchEmployees($this->db);
        $this->assertEquals($expectedResult, $employees);
    }

    public function testUpdateUserUpdatesPasswordAndOfficeId() {
        $userId = 1;
        $password = 'newpassword';
        $officeId = 101;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);

        $stmt->expects($this->exactly(2))
            ->method('bind_param')
            ->withConsecutive(
                [$this->equalTo('si'), $this->equalTo($password), $this->equalTo($userId)],
                [$this->equalTo('ii'), $this->equalTo($officeId), $this->equalTo($userId)]
            );

        User::updateUser($this->db, $userId, $password, $officeId);
    }

    public function testUpdateUserDoesNotUpdateIfUserIdIsEmpty() {
        $userId = null;
        $password = 'newpassword';
        $officeId = 101;

        $this->db->expects($this->never())->method('prepare');

        ob_start();
        User::updateUser($this->db, $userId, $password, $officeId);
        $output = ob_get_clean();

        $this->assertEquals("Error: User ID is required.", $output);
    }

    public function testUpdateUserDoesNotUpdateIfNoChangesMade() {
        $userId = 1;
        $password = null;
        $officeId = null;

        $stmt = $this->createMock(mysqli_stmt::class);
        $this->db->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);

        $stmt->expects($this->once())
            ->method('bind_param')
            ->with($this->equalTo('i'), $this->equalTo($userId));

        $stmt->method('affected_rows')->willReturn(0);

        ob_start();
        User::updateUser($this->db, $userId, $password, $officeId);
        $output = ob_get_clean();

        $this->assertEquals("No changes made or user not found.", $output);
    }
}