<?php
/**
 * Migration: Create stored procedure for safe product deletion
 * 
 * This migration creates a stored procedure to safely delete products
 * with proper checks for dependencies.
 */

require_once __DIR__ . '/../../bootstrap/app.php';

echo "🔄 Creating stored procedure for safe product deletion...\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
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
        -- Check if product has sale items
        ELSEIF EXISTS (SELECT 1 FROM sale_items WHERE product_id = product_id) THEN
            SET result_message = 'Cannot delete product with existing sales';
            SET result_code = -3;
        -- Check if product has stock movements
        ELSEIF EXISTS (SELECT 1 FROM stock_movements WHERE product_id = product_id) THEN
            SET result_message = 'Cannot delete product with stock movement history';
            SET result_code = -4;
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
    echo "✅ Stored procedure 'safe_delete_product' created successfully!\n";
    echo "\n📝 Usage:\n";
    echo "   CALL safe_delete_product(123, @result_code, @result_message);\n";
    echo "   SELECT @result_code, @result_message;\n";
    
} catch (Exception $e) {
    echo "❌ Failed to create stored procedure: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Migration completed successfully!\n";
?>