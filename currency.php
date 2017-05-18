<?php 
//Model
class CurrencyConverter {
    private $baseValue = 0;
    
    private $rates = [
        'GBP' => 1.0,
        'USD' => 0.6,
        'EUR' => 0.83,
        'YEN' => 0.0058,
        'BTC' => 0.0099
    ];
    
    public function get($currency) {
        if (isset($this->rates[$currency])) {
            $rate = 1/$this->rates[$currency];
            return round($this->baseValue * $rate, 2);
        }
        else return 0;        
    }
    
    public function set($amount, $currency = 'GBP') {
        if (isset($this->rates[$currency])) {
            $this->baseValue = $amount * $this->rates[$currency];
        }
    }
    
}


//View 
class CurrencyConverterView {
    private $converter;
    private $currency;
    
    public function __construct(CurrencyConverter $converter, $currency) {
        $this->converter = $converter;
        $this->currency = $currency;
    }
    
    public function output($currency) {
        $strong = '';
        if ($currency == $this->currency) {
          $strong = "style='font-weight: bold;'";
        }
        $html = '<form action="?action=convert" method="post">
                    <input name="currency" type="hidden" value="' . $this->currency .'" />
                    <label '. $strong . '>' . $this->currency .':</label>
                    <input name="amount" type="text" value="' . $this->converter->get($this->currency) . '" />
                    <input type="submit" value="Convert" />                
                </form>';
        
        return $html;
    }
}


//Controller
class CurrencyConverterController {
    private $model;
    
    public function __construct($model) {
        $this->model = $model;
    }
    
    public function convert($request) {
        if (isset($request['currency']) && isset($request['amount'])) {
            $this->model->set($request['amount'], $request['currency']);
        }
    }
}


//Application initialisation/entry point.
$model = new CurrencyConverter();
$controller = new CurrencyConverterController($model);

//If one of the forms has been submitted, call the relevant controller action
if (isset($_GET['action'])) {
  $controller->{$_GET['action']}($_POST);
}
else {
  $_POST['currency'] = '';
}
  $gbpView = new CurrencyConverterView($model, 'GBP');
  echo $gbpView->output($_POST['currency']);

  $usdView = new CurrencyConverterView($model, 'USD');
  echo $usdView->output($_POST['currency']);

  $eurView = new CurrencyConverterView($model, 'EUR');
  echo $eurView->output($_POST['currency']);

  $yenView = new CurrencyConverterView($model, 'YEN');
  echo $yenView->output($_POST['currency']);

  $btcView = new CurrencyConverterView($model, 'BTC');
  echo $btcView->output($_POST['currency']);
