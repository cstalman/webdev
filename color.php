<?php

//Model
class ColorConverter {

    private static $baseValue = 0;
    private static $steps = 10;
    private static $mode = 'dark';

    public function get() {
      $color = self::$baseValue;
      $steps = self::$steps;
      $mode = self::$mode;

      // Split into three parts: R, G and B
      $color_parts = str_split(substr($color,1), 2);
      $return = '#';

      foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        if($mode == 'light') {
          $color   = max(0,min(255,$color + $steps)); // lighten color
        }   
        if($mode == 'dark') {
          $color   = max(0,min(255,$color - $steps)); // darken color
        }
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
      }

      self::$baseValue = $return;
      return self::$baseValue;
    }
    
    public function set($mode, $color) {
      self::$baseValue = $color;
      self::$mode = $mode;
    }

}

// View
class ColorOutputView {

    private $converter;
    private $color;

    public function __construct(ColorConverter $converter) {
        $this->converter = $converter;
    }

    public function output() {
      $color = $this->converter->get($this->color);
      $html = '<div style="background-color:'.$color.';width:200px;height:30px" title="'.$color.'">
              </div>';

      return $html;
    }

}

class ColorInputView {
  private $color = '#000000';
  
  public function output() {
    if (isset($_POST['color'])) {
      $this->color = $_POST['color'];
    }
        $html = '<form action="?action=convert" method="post">
                    <label>Basiskleur: </label>
                    <input name="color" type="color" value="' . $this->color . '" />
                    <input type="submit" name="light" value="Lichter" />
                    <input type="submit" name="dark" value="Donkerder" />
                </form>';

        return $html;
    } 
  
}

// Controller
class ColorController {

    private $model;
    private $counter = 20;

    public function __construct($model) {
        $this->model = $model;
    }

    public function convert($request) {
        if (isset($request['dark'])) {
            $this->model->set('dark', $request['color']);
        }
        elseif (isset($request['light'])) {
            $this->model->set('light', $request['color']);
        }
    }
    
    public function getCounter() {
      return $this->counter;
    }
	
}

// Input view
$inputView = new ColorInputView();
echo $inputView->output();


// Application initialisation/entry point.
$model = new ColorConverter();
$controller = new ColorController($model);

// If the form has been submitted, call the relevant controller action
if (isset($_GET['action'])) {
    $controller->{$_GET['action']}($_POST);
    
    // output as many views as defined in the controller
    for($i=0;$i<$controller->getCounter();$i++){  
      $View1 = new ColorOutputView($model);
      echo $View1->output();
    }
}
