<?php

class CoreExt_View_TableHelperTest extends PHPUnit\Framework\TestCase
{
    protected $_headerExpected = '
<thead>
  <tr>
    <td>Title</td>
    <td colspan="2">Item A</td>
    <td colspan="2">Item B</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Subitem A1</td>
    <td>Subitem A2</td>
    <td>Subitem B1</td>
    <td>Subitem B2</td>
  </tr>
</thead>';

    protected $_bodyExpected = '
<tbody>
  <tr>
    <td>Label 1</td>
    <td>1</td>
    <td>2</td>
    <td>3</td>
    <td>4</td>
  </tr>
  <tr>
    <td>Label 2</td>
    <td>1</td>
    <td>2</td>
    <td>3</td>
    <td>4</td>
  </tr>
</tbody>';

    protected $_footerExpected = '
<tfooter>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td class="tdd" colspan="2">1</td>
  </tr>
</tfooter>';

    protected $_tableExpected = '
<table>
  <tbody>
    <tr>
      <td>Example data</td>
    </tr>
  </tbody>
</table>
';

    protected $_tableRowAttributesExpected = '
<table>
  <tbody>
    <tr class="trr">
      <td>Example data</td>
    </tr>
  </tbody>
</table>
';

    protected function setUp(): void
    {
    }

    public function testCreateHeader()
    {
        $table = CoreExt_View_Helper_TableHelper::getInstance();

        $cols = [
            ['data' => 'Title'],
            ['data' => 'Item A', 'colspan' => 2],
            ['data' => 'Item B', 'colspan' => 2]
        ];

        $cols2 = [
            [],
            ['data' => 'Subitem A1'],
            ['data' => 'Subitem A2'],
            ['data' => 'Subitem B1'],
            ['data' => 'Subitem B2']
        ];

        $table->addHeaderRow($cols);
        $table->addHeaderRow($cols2);

        $header = $table->createHeader();
        $this->assertEquals(trim($this->_headerExpected), $header);
    }

    public function testCreateBody()
    {
        $table = CoreExt_View_Helper_TableHelper::getInstance();

        $cols = [
            ['data' => 'Label 1'],
            ['data' => '1'],
            ['data' => '2'],
            ['data' => '3'],
            ['data' => '4']
        ];

        $cols2 = [
            ['data' => 'Label 2'],
            ['data' => '1'],
            ['data' => '2'],
            ['data' => '3'],
            ['data' => '4']
        ];

        $table->addBodyRow($cols);
        $table->addBodyRow($cols2);

        $body = $table->createBody();
        $this->assertEquals(trim($this->_bodyExpected), $body);
    }

    public function testCreateFooter()
    {
        $table = CoreExt_View_Helper_TableHelper::getInstance();

        $cols = [
            ['colspan' => 3],
            ['data' => '1', 'colspan' => 2, 'attributes' => ['class' => 'tdd']],
        ];

        $table->addFooterRow($cols);

        $footer = $table->createFooter();
        $this->assertEquals(trim($this->_footerExpected), $footer);
    }

    public function testCreateTable()
    {
        $table = CoreExt_View_Helper_TableHelper::getInstance();
        $table->addBodyRow([['data' => 'Example data']]);
        $table = $table->createTable();
        $this->assertEquals(trim($this->_tableExpected), $table);
    }

    public function testRowAttributes()
    {
        $table = CoreExt_View_Helper_TableHelper::getInstance();
        $table->addBodyRow([['data' => 'Example data']], ['class' => 'trr']);
        $table = $table->createTable();
        $this->assertEquals(trim($this->_tableRowAttributesExpected), $table);
    }
}
