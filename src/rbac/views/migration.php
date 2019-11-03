<?php
/**
 * @var $className string the new migration class name
 */
echo "<?php\n";
?>

use rvkulikov\amo\module\rbac\Migration;

/**
*
*/
class <?= $className; ?> extends Migration
{
public function safeUp()
{
$this->create
}

public function safeDown()
{
$this->remove
}
}