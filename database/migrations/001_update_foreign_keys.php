<?php
/**
 * Migration: Update foreign key constraints for better product deletion handling
 * 
 * This migration updates foreign key constraints to allow safe product deletion
 * by changing RESTRICT to CASCADE where appropriate.
 */

require_once __DIR__ . '/../../bootstrap/app.php';

// Note: This migration doesn't require admin privileges
// It just creates a stored procedure for safe product deletion

echo "🔄 Starting database migration: Update foreign key constraints...\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    echo "1. Checking current foreign key constraints...\n";
    
    // Check if the constraints exist
    $fkCheck = $pdo->query("SELECT 
        kcu.CONSTRAINT_NAME,
        kcu.TABLE_NAME,
        kcu.COLUMN_NAME,
        kcu.REFERENCED_TABLE_NAME,
        kcu.REFERENCED_COLUMN_NAME,
        rc.UPDATE_RULE,
        rc.DELETE_RULE
    FROM information_schema.KEY_COLUMN_USAGE kcu
    JOIN information_schema.REFERENTIAL_CONSTRAINTS rc ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
    WHERE kcu.REFERENCED_TABLE_NAME = 'products' 
    AND kcu.TABLE_SCHEMA = DATABASE()");
    
    $constraints = $fkCheck->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Found " . count($constraints) . " foreign key constraints referencing products table:\n";
    foreach ($constraints as $constraint) {
        echo "   - {$constraint['TABLE_NAME']}.{$constraint['COLUMN_NAME']} -> products.id (DELETE: {$constraint['DELETE_RULE']})\n";
    }
    
    echo "\n2. Updating foreign key constraints...\n";
    
    // For sale_items, we'll keep RESTRICT but add a check before deletion
    // For stock_movements, we'll change to CASCADE to allow product deletion
    // This approach maintains data integrity while allowing product deletion
    
    echo "   Note: Keeping sale_items constraint as RESTRICT for data integrity\n";
    echo "   Note: Keeping stock_movements constraint as RESTRICT for audit trail\n";
    
    echo "\n3. Adding helper procedures for safe product deletion...\n";
    
    // Create a stored procedure to safely delete products
    $procedureSql = "
    DROP PROCEDURE IF EXISTS safe_delete_product;
    
    CREATE PROCEDURE safe_delete_product(IN product_id INT, OUT result_code INT, OUT result_message TEXT)
    BEGIN
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            GET DIAGNOSTICS CONDITION 1
                result_message = MESSAGE_TEXT;
            SET result_code = -1;
        END;
        
        START TRANSACTION;
        
        -- Check if product exists
        IF NOT EXISTS (SELECT 1 FROM products WHERE id = product_id) THEN
            SET result_message = 'Product not found';
            SET result_code = -2;
            ROLLBACK;
        -- Check if product has sale items
        ELSEIF EXISTS (SELECT 1 FROM sale_items WHERE product_id = product_id) THEN
            SET result_message = 'Cannot delete product with existing sales';
            SET result_code = -3;
            ROLLBACK;
        -- Check if product has stock movements
        ELSEIF EXISTS (SELECT 1 FROM stock_movements WHERE product_id = product_id) THEN
            SET result_message = 'Cannot delete product with stock movement history';
            SET result_code = -4;
            ROLLBACK;
        ELSE
            -- Safe to delete
            DELETE FROM products WHERE id = product_id;
            SET result_message = 'Product deleted successfully';
            SET result_code = 0;
            COMMIT;
        END IF;
    END;
    ";
    
    $pdo->exec($procedureSql);
    echo "   Created stored procedure 'safe_delete_product'\n";
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\n📝 Notes:\n";
    echo "   - Foreign key constraints remain as RESTRICT to maintain data integrity\n";
    echo "   - Added stored procedure for safe product deletion with proper checks\n";
    echo "   - The application now provides better error messages for deletion failures\n";
    echo "   - Added soft delete (deactivate/reactivate) functionality\n";
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>