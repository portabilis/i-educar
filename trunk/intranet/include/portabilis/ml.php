<?php

/*
Copyright (c) 2011 Lucas D'Avila - email <lucassdvl@gmail.com> / twitter @lucadavila

This file is part of fraap framework.

fraap framework is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License (LGPL v3) as published by
the Free Software Foundation, on version 3 of the License.

fraap framework is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with appreport.  If not, see <http://www.gnu.org/licenses/>.
*/

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

class MarkupLanguage
{
  
  function __construct()
  {
    #TODO mover para construtor HtmlValidatableElement
    $this->requires = array();
    $this->errors = array(); 
    $this->_vars = array();

    $this->encoding = '';
    $this->_childElements = array();
    $this->_attributes = array();

    foreach(func_get_args() as $a)
    {
      if (is_array($a))
      {
        foreach($a as $k => $v)
          $this->appendAtt($k, $v);
      }
      else //if ($a instanceof MarkupLanguage || is_string($a))
        $this->append($a);  
    }
  }

  function append($element)
  {
    $this->_childElements[] = $element;
  }

  function appendAtt($attName, $attValue)
  {
    $this->_attributes[$attName] = $attValue;
  }

  function hasAtt($attName)
  {
    return array_key_exists($attName, $this->_attributes);
  }

  function getAtts()
  {
    return $this->_attributes;
  }

  function getAttValue($attName)
  {
    if ($this->hasAtt($attName))
      return $this->_attributes[$attName];
    else
      return null;
  }

  function setAttValue($attName, $attValue)
  {
    $this->_attributes[$attName] = $attValue;
  }

  function getChildElements()
  {
    return $this->_childElements;
  }

  /*abstract*/ function render()
  {
  }

  /*Returns adicional elements or information to render() function*/
  function subRender()
  {
    return '';
  }

  /*abstract*/ function getTag()
  {
  }

  function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }

  function getElementsByTagName($tagName)
  {
    $elements = array();
    foreach($this->getChildElements() as $e)
    {
      if ($e instanceof MarkupLanguage && $e->getTag() == $tagName)
        $elements[] = $e;
      else if ($e instanceof MarkupLanguage)
        $elements = array_merge($e->getElementsByTagName($tagName), $elements);
    }
    return $elements;
  }

  function getElementById($id)
  {
    foreach($this->getChildElements() as $e)
    {
      if ($e instanceof MarkupLanguage && $e->getAttValue('id') == $id)
        return $e;
      else if ($e instanceof MarkupLanguage)
        return $e->getElementById($id);
    }
    return null;
  }
}


class XmlDocument extends MarkupLanguage
{

  function getHeader()
  {
    return printf("<?xml version=\"1.0\" encoding=%s?> <%s>", $this->encoding, $this->getTag());
  }

  function getTag()
  {
    return 'xml';
  }
  
  function render()
  {
    $t = $this->getHeader();
    foreach ($this->getChildElements() as $e)
      $t .= $e->render();
    echo $t . "</{$this->getTag()}>";
  }
}

class XmlElement extends XmlDocument
{

  function render()
  {
    $att = '';
    foreach($this->getAtts() as $k => $v)
      if ($v)
        $att .= " $k = '$v'";

    $t = "<{$this->getTag()} $att>";   
    foreach ($this->getChildElements() as $e)
    {
      if ($e instanceof MarkupLanguage)
        $t .= $e->render();
      else
        $t .= $e;
    }
    $t .= $this->subRender();
    $t .= "</{$this->getTag()}>";
    return $t;
  }
}

/*class HtmlDocument extends XmlDocument
{
  $this->tag = 'html';
  $this->header = '<$this->tag>';
}
*/
class HtmlElement extends XmlElement
{

  function getHeader()
  {
    return "<$this->getTag()>";
  }
  
  function getTag()
  {
    return 'html';
  }
}

class HtmlValidatableElement extends HtmlElement
{
 
  function __Aconstruct()
  {
    $this->requires = array();
    $this->errors = array();  

    #var_dump(func_get_args());
    #FIXME ocorre erro 
    //call_user_func_array(array($this, 'parent::__construct'), $args); 
//    call_user_func_array(array($this, 'parent::__construct'), func_get_args()); 
  }

  function validate($value, $showAllErrors = false)
  {
    $this->setAttValue('old_value', $this->getAttValue('value'));
    $this->errors = array();
    $validatedValue = $value;
    foreach ($this->requires as $validation)
    { 
      #echo "validator of element {$this->getAttValue('name')}: ";
      #var_dump($validation);
      #echo '<br />';
      
      $e = $validation->validate($value);
      $validatedValue = $e[0];
      if (isset($e[1]) && $e[1])
      {
        $this->errors[] = $e;
        if (! $showAllErrors)
          break;
      }
    }

    /* (count($this->errors))
    {
      echo '<br />';
      echo "errors of element {$this->getAttValue('name')}: ";
      var_dump($this->errors);
      echo '<br />';
    }*/

    $this->setAttValue('value', $value);#TODO verificar onde o valor deste atributo é usado, pode-se pegar das vars ?
    $this->_vars[$this->getAttValue('name')] = $validatedValue;
    
    return $this->errors;
  }

  function subRender()
  {
//    $t = call_user_func_array(array($this, 'parent::subRender'), func_get_args()); #FIXME ocorre erro ao chamar...
    $t='';
    foreach ($this->errors as $error)
    {
      $p = new P($error[1], array('class' => 'error', 'id' => "{$this->getAttValue('name')}_error"));
      $t .= $p->render();
    }
    return $t;
  }
}

class Input extends HtmlValidatableElement
{

  function getAtts()
  {
    if (! $this->hasAtt('type'))
      $this->appendAtt('type', 'text');
    return $this->_attributes;
  }

  function getTag()
  {
    return 'input';
  }
}

class P extends HtmlElement
{
  function getTag()
  {
    return 'p';
  }
}

class H1 extends HtmlElement
{
  function getTag()
  {
    return 'h1';
  }
}

class H2 extends HtmlElement
{
  function getTag()
  {
    return 'h2';
  }
}

class H3 extends HtmlElement
{
  function getTag()
  {
    return 'h3';
  }
}

class H4 extends HtmlElement
{
  function getTag()
  {
    return 'h4';
  }
}

class H5 extends HtmlElement
{
  function getTag()
  {
    return 'h5';
  }
}


class HtmlTable extends HtmlElement #error redeclare if use 'table' ?
{
  function getTag()
  {
    return 'table';
  }
}

class TD extends HtmlElement
{
  function getTag()
  {
    return 'td';
  }
}

class TR extends HtmlElement
{
  function getTag()
  {
    return 'tr';
  }
}

class TH extends HtmlElement
{
  function getTag()
  {
    return 'th';
  }
}

class A extends HtmlElement
{
  function getTag()
  {
    return 'a';
  }
}

class DIV extends HtmlElement
{
  function getTag()
  {
    return 'div';
  }
}

class STRONG extends HtmlElement
{
  function getTag()
  {
    return 'strong';
  }
}

class SPAN extends HtmlElement
{
  function getTag()
  {
    return 'span';
  }
}

class Form extends HtmlElement
{

  function getTag()
  {
    return 'form';
  }

  function getAtts()
  {
    if (! $this->hasAtt('method'))
      $this->appendAtt('method', 'POST');
    return $this->_attributes;
  }

  function accepts($vars, $session = null, $fieldsIgnored=array(), $showAllErrors = false)
  {

    if (! count($vars))
      return false;

    if (! isset($this->_vars))
      $this->_vars = $vars;

    $errors = array();
    if (count($this->_vars))
    {
      $inputs = $this->getElementsByTagName('input');
      #TODO ? $inputs = array(merge_array($this->getElementsByTagName('select'), $inputs));
      #TODO ? $inputs = array(merge_array($this->getElementsByTagName('textarea'), $inputs));
      #TODO ou criar metodo $this->getElementsByTagNames('input', 'select', 'textarea');

      foreach($inputs as $i)
      {
        $name = $i->getAttValue('name');
        if ($name)
        {
          $validate = true;

          foreach($fieldsIgnored as $ig)
            if ($name == $ig)
              $validate = false;

          if ($validate)
          {
            $i->_vars = $this->_vars;
            $e = $i->validate($this->_vars[$i->getAttValue('name')], $showAllErrors);
            $this->_vars = $i->_vars;#POG ?
            if (count($e))
              $errors[] = $e;
          }
        
        }
      }
    }
    return count($errors) < 1;
  }

}

class SqlForm extends Form
{

  function __construct($table, $record_id = null)
  {
    $this->record_id = $record_id;
    $this->table = $table;
    $this->newRecord = $this->record_id == null;

    if ($this->table->hasField('id'))
    {
      $table->field('id')->writable = false; 
      $table->field('id')->readable = ! $this->newRecord;
          
      if (/*(! isset($this->_vars)) && */ ! $this->newRecord)
      {
        #TODO não fazer novo select, se request.vars
        $this->record = $this->table->select("{$this->table->name}.id = $this->record_id");

        if (! count($this->record))
          die('Invalid id');
        else
          $this->record = $this->record[0];
      }
      //else if (isset($this->_vars))
        //$this->record = $this->_vars;
      else
        $this->record = array();
    }

    $_table = new HtmlTable();
    foreach ($table->fields as $f)
    {
      if ($f->readable)
      {

        $types = array();
        $types['date'] = 'text';
        $types['string'] = 'text';
        $types['integer'] = 'text';
        $types['password'] = 'password';
        $types['text'] = 'textarea';

        $fieldValue = isset($this->record[$f->name]) ? $this->record[$f->name] : '';#TODO pegar field->default

        if (/*($this->record_id && $f->name == 'id') || */! $f->writable)
          $readonly = 'readonly';
        else
          $readonly = '';

        $_input = new Input(array('type' => $types[$f->type], 'name' => 'field_' . $f->name, 'id' => "{$table->name}_$f->name", 'value' => $fieldValue, 'readonly' => $readonly));
        $_input->requires = array_merge($_input->requires, $f->requires);      

        $_table->append(new TR(new TD(str_replace('_', ' ', ucwords($f->label ? $f->label : $f->name)), 
                               new TD($_input))));
      }
    }
     $_table->append(new TR(new TD(), new TD(new input(array('type'=>'submit', 'value'=>'Gravar', 'name' => "submit_form_$table->name")))));
 
    $args = array_slice(func_get_args(), 2);
    $args[] = $_table;

    call_user_func_array(array($this, 'parent::__construct'), $args); 
  }

  function accepts($vars, $session = null, $fieldsIgnored=array(), $showAllErrors = false)
  {

    $this->_vars = $vars;

    if (count($vars))
    {

      foreach($this->table->fields as $f)
      {
        if (! $f->readable)
          $fieldsIgnored = array_merge($fieldsIgnored, array("field_$f->name"));
      }

      if (parent::accepts($this->_vars, $session, $fieldsIgnored, $showAllErrors))
      {

        foreach($this->table->fields as $f)
        {
          if (isset($this->_vars["field_$f->name"]))
            $f->value = $this->_vars["field_$f->name"];            
        }

        if ($this->newRecord)
          $result = $this->table->insert($this->table->fields); #TODO pegar valor para $this->record_id
        else if ((! $this->newRecord) && $this->table->hasField('id'))
          $result = $this->table->update("{$this->table->name}.id = $this->record_id", $this->table->fields);
        else
          die("Ops, column 'id' not defined on table $this->table->name");

        return true;
      }
    }
    return false;
  }
}

/*TODO
function A(*args, **kargs)
{
  return new A(args, kargs);
}
*/

?>
