<?php
/**
 * {$migration_name|camelize} Migration.
 *
 * @package    Silla.IO
 * @subpackage DB\Migrations
 * @author     Author <author@website.com>
* @copyright  Copyright (c) 2015, Silla.io
* @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
*/

namespace DB\Migrations;

use Core\Modules\DB;

/**
* {$migration_name|camelize} class definition.
*/
class {$migration_name|camelize
} extends DB\Migration
{
{if {
    $name.0 eq 'create' and $name.1 eq 'table'
}
  {if {
    $name.1 eq 'table'
  }
    }
    /**
    * Up method.
    *
    * @return void
    */
    public function up()
    {
        $this->createTable('{$name.2|tableize}', array(
        {foreach {
            from=$fields item=field
        }
        '{$field.0}' => 'type:{$field.1}',
        {/foreach {
        }
        }
        ));
        }
    }
    }

    /**
    * Down method.
    *
    * @return void
    */
    public function down()
    {
        $this->dropTable('{$name.2|tableize}');
    }
    {/if {
    }
    {/if {
    }
    }
    {if {
        $name.0 eq 'add'
    }
    }
    {if {
        $name.1 eq 'columns'
    }
    }
    /**
    * Up method.
    *
    * @return void
    */
    public function up()
    {
        $this->createColumns('{$name.3|tableize}', array(
        {foreach {
            from=$fields item=field
        }
        '{$field.0}' => 'type:{$field.1}',
        {/foreach {
        }
        }
        ));
        }
    }
    }

    /**
    * Down method.
    *
    * @return void
    */
    public function down()
    {
        $this->dropColumns('{$name.3|tableize}', array({foreach {
            from=$fields item=field
        }'{$field.0}',{/foreach {
        }
        }));
        }
    }
    {/if {
    }
    {/if {
    }
    }
    }
    }
