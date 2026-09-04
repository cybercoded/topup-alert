<?php
/**
 * Shared transaction processor for customer services
 * Simulates third-party API success and updates wallet + transactions
 */
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

function processPayment(array $data): array {
    $db = getDB();
    $customerId = $_SESSION['customer_id'];

    // Get current balance
    $stmt = $db->prepare("SELECT wallet_balance FROM customers WHERE id = ? FOR UPDATE");
    $db->beginTransaction();
    try {
        $stmt->execute([$customerId]);
        $balance = (float)$stmt->fetchColumn();

        $amount = (float)$data['amount'];
        $fee = (float)($data['fee'] ?? 0);
        $total = $amount + $fee;

        if ($total <= 0) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Invalid amount'];
        }

        if ($balance < $total) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Insufficient wallet balance. You need ' . formatNaira($total) . ' but have ' . formatNaira($balance)];
        }

        $ref = generateRef('TXN');
        $providerRef = 'PRV' . random_int(100000000, 999999999);

        // Insert transaction
        $stmt = $db->prepare("
            INSERT INTO transactions (
                transaction_ref, customer_id, service_type_id, amount, fee, total_amount,
                currency, status, provider_ref, beneficiary_phone, beneficiary_meter,
                beneficiary_smartcard, network, data_plan, disco, cable_provider,
                payment_method, description, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'NGN', 'successful', ?, ?, ?, ?, ?, ?, ?, ?, 'wallet', ?, NOW())
        ");
        $stmt->execute([
            $ref,
            $customerId,
            $data['service_type_id'],
            $amount,
            $fee,
            $total,
            $providerRef,
            $data['beneficiary_phone'] ?? null,
            $data['beneficiary_meter'] ?? null,
            $data['beneficiary_smartcard'] ?? null,
            $data['network'] ?? null,
            $data['data_plan'] ?? null,
            $data['disco'] ?? null,
            $data['cable_provider'] ?? null,
            $data['description']
        ]);
        $txnId = $db->lastInsertId();

        // Debit wallet
        $newBalance = $balance - $total;
        $stmt = $db->prepare("UPDATE customers SET wallet_balance = ? WHERE id = ?");
        $stmt->execute([$newBalance, $customerId]);

        // Ledger entry
        $stmt = $db->prepare("
            INSERT INTO wallet_transactions (customer_id, transaction_id, type, amount, balance_before, balance_after, description)
            VALUES (?, ?, 'debit', ?, ?, ?, ?)
        ");
        $stmt->execute([$customerId, $txnId, $total, $balance, $newBalance, $data['description']]);

        $db->commit();

        return [
            'success' => true,
            'message' => 'Payment successful!',
            'ref' => $ref,
            'provider_ref' => $providerRef,
            'new_balance' => $newBalance,
            'total' => $total
        ];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()];
    }
}
?>
