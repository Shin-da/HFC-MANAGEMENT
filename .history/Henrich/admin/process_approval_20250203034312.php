// or wherever your approval logic is located

$stmt = $conn->prepare("
    INSERT INTO approved_account (username, first_name, last_name, usermail, role, status)
    SELECT username, first_name, last_name, usermail, role, 'active'
    FROM account_request
    WHERE user_id = ? AND status = 'pending'
");
$stmt->bind_param("i", $user_id);
