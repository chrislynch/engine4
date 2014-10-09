<?php

class _cart {

    public $totals;
    public $items;
    public $services;
    public $order;
    public $tracking;
    
    private $e;
    
    public function __construct(&$e){
        $this->e =& $e;
        
        $this->e->_loadplugin('config');
        $this->e->_loadplugin('messaging');
        $this->e->_loadplugin('db');
        $this->e->_loadplugin('cryptography');
        
        $this->totals = new stdClass();
        $this->totals->items = 0;
        $this->totals->value = '0.00';
        $this->items = array();
        $this->tracking = '';
        
        $this->addToCart();
        $this->updateCart();
        $this->loadCart('product');
        $this->totalCart();
        $this->applyServices();
        $this->loadCart();  // Reload cart after services have been applied
        $this->totalCart();
        
    }
    
    public static function addToCartFormValue($Code,$Price,$QTY = 1,$Title = '',$Image = '',$Other = array()){
        $data = array();
        $data['Code'] = $Code;
        $data['Price'] = $Price;
        $data['QTY'] = $QTY;
        if ($Title == ''){ $data['Title'] = $Code; } else { $data['Title'] = $Title; }
        $data['Image'] = $Image;
        foreach($Other as $otherKey=>$otherValue){
            $data[$otherKey] = $otherValue;
        }
        include_once('_e/plugins/cryptography/cryptography.php');
        return _cryptography::encrypt(serialize($data));
        
    }
    
    private function addToCart(){
        if (isset($_REQUEST['e4cartAdd'])){
            $flexcartData = $_REQUEST['e4cartAdd'];
            $flexcartDataSerial = $this->e->_cryptography->decrypt($flexcartData);
            $flexcartDataSerial = stripslashes($flexcartDataSerial);
            $flexcartDataSerial = str_replace("\n","",$flexcartDataSerial); 
            $flexcartData = unserialize($flexcartDataSerial);
            
            if(isset($flexcartData['Code']) && isset($flexcartData['Price'])){
                if (!isset($flexcartData['Type'])) { $flexcartData['Type'] = 'product'; }
                if (!isset($flexcartData['QTY'])) { $flexcartData['QTY'] = 1; }
                if (!isset($flexcartData['Title'])) { $flexcartData['Title'] = $flexcartData['Code']; }
                if (!isset($flexcartData['Description'])) { $flexcartData['Description'] = ''; }

                $SQL = "insert into trn_cart
                                        set session_id = '" . session_id() . "',
                                            Type = '{$flexcartData['Type']}',
                                            Code='{$flexcartData['Code']}',
                                            QTY={$flexcartData['QTY']},
                                            Price={$flexcartData['Price']},
                                            Title='{$flexcartData['Title']}',
                                            Description='{$flexcartData['Description']}',
                                            Data = '$flexcartDataSerial'
                                        on duplicate key update QTY= QTY + {$flexcartData['QTY']};";
                
                $this->e->_db->insert($SQL);

            }    
        }
    }

    private function updateCart(){
        if (isset($_REQUEST['e4cartUpdate'])){
            
            $sessionID = session_id();
            if(isset($_REQUEST['e4cartUpdate']) && isset($_REQUEST['e4cartUpdateQTY'])){
                if ($_GET['e4cartUpdateQTY'] == 0){
                    $SQL = "DELETE FROM trn_cart WHERE Code = '{$_REQUEST['e4cartUpdate']}' AND session_id = '$sessionID'";
                } else {
                    $SQL = "UPDATE trn_cart SET QTY = {$_REQUEST['e4cartUpdateQTY']} WHERE Code = '{$_REQUEST['e4cartUpdate']}' AND session_id = '$sessionID'";
                }
                $this->e->_db->update($SQL);
            }
        }
    }
    
    public function emptyCart(){
        $sessionID = session_id();
        $SQL = "DELETE FROM trn_cart WHERE session_id = '$sessionID'";
        $this->e->_db->update($SQL);
        $this->loadCart();
        $this->totalCart();
    }
    
    private function loadCart($typefilter = ''){
        // Clear the current items array
        
        $this->items = array();

        if (strlen($typefilter) > 0){
			$serviceSQL = 'SELECT * FROM trn_cart WHERE session_id = "' . session_id() . '"';
			$serviceSQL .= " AND Type = '$typefilter'";
        } else {
        	$serviceSQL = 'SELECT * FROM trn_cart WHERE session_id = "' . session_id() . '" AND Type = "product" 
        					UNION
        				   SELECT * FROM trn_cart WHERE session_id = "' . session_id() . '" AND Type <> "product"';
        }
        
        $servicesData = $this->e->_db->query($serviceSQL);
        
        while($serviceRow = mysql_fetch_assoc($servicesData)){
            $service = new cartItem();
            $service->Type = $serviceRow['Type'];
            $service->Code = $serviceRow['Code'];
            $service->QTY = $serviceRow['QTY'];
            $service->Price = $serviceRow['Price'] * $service->QTY;
            $service->Title = $serviceRow['Title'];
            $service->Description = $serviceRow['Description'];
            $service->Data = $serviceRow['Data'];
            if($type == 'shipping'){
                $service->calculateTax(FALSE);
            } else {
                $service->calculateTax(TRUE);
            }
            
            $this->items[$serviceRow['Code']] = $service; 
        }
        
    }
    
    private function applyServices(){
    	// Apply service lines to this cart.
    	// Shipping and discounts are our normal service lines

    	if(sizeof($this->items) > 0){
    		// Check for discount function and apply
    		if(function_exists('cart_get_discount_custom')){
    			$discount = cart_get_discount_custom($this);
    		} else {
    			$discount = $this->cart_get_discount();
    		}
    		 
    		if($discount['Price'] < 0){
    			$this->addService($discount,'discount');
    			$this->totalCart(); // Total the cart in case shipping is value based.
    		}

    		// Check for a shipping function and apply
    		if(function_exists('cart_get_shipping_custom')){
    			$shipping = cart_get_shipping_custom($this);
    		} else {
    			$shipping = $this->cart_get_shipping();
    		}
    		$this->addService($shipping,'shipping');
    		
    	} else {
    		// Cart is empty of products. Make sure no services linger
    		$this->e->_db->delete("delete from trn_cart where session_id = '" . session_id() . "' AND Type = 'shipping'");
    		$this->e->_db->delete("delete from trn_cart where session_id = '" . session_id() . "' AND Type = 'discount'");
    	}
    		   	
    }
    
    private function cart_get_shipping(){
    	$return = array('Code' => 'SHIPPING','Title'=>'Standard Shipping','Description'=>'','Price'=>0.00);
    	return $return;
    }
    
    private function cart_get_discount(){
    	$return = array('Price' => 0.00,'Title'=>'Discount');
    	return $return;
    }
    
    private function addService($newservice,$type){
    	$service = new cartItem();
    	$service->Type = $type;
    	$service->Code = $newservice['Code'];
    	$service->QTY = 1;
    	$service->Price = $newservice['Price'] * $service->QTY;
    	$service->Title = $newservice['Title'];
    	$service->Description = $newservice['Description'];
    	$service->Data = @$newservice['Data'];
        if($type == 'shipping'){
            $service->calculateTax(FALSE);
        } else {
            $service->calculateTax(TRUE);
        }
    	 
    	$dSQL = "delete from trn_cart where session_id = '" . session_id() . "' AND Type = '$type'";
    	$iSQL = "insert into trn_cart
					set session_id = '" . session_id() . "',
                    Type = '{$service->Type}',
                    Code='{$service->Code}',
                    QTY={$service->QTY},
                    Price={$service->Price},
                    Title='{$service->Title}',
                    Description='{$service->Description}',
                    Data = '{$service->Data}'
                    ";
                
    	$this->e->_db->delete($dSQL);
		$this->e->_db->insert($iSQL);
    }
    
    private function totalCart(){
        $this->totals->items = 0;
        $this->totals->value = 0.00;
        foreach($this->items as $cartthing){
        	if($cartthing->Type == 'product'){
            	$this->totals->items += $cartthing->QTY;
        	}
            $this->totals->value += $cartthing->Price;
        }
        $this->totals->value = number_format($this->totals->value,2);
    }
    
    public function createOrder(){
        // Order the contents of the cart.
        $this->order = new cartOrder($this->e);
        $this->order->ID = uniqid();
        $this->order->saveHeader($_POST);
        $this->order->saveProducts($this->items);
    }

    public function AddTracking(){
        $GoogleTracking = '<!-- Google Code for Order Conversion Page -->
                            <script type="text/javascript">
                            /* <![CDATA[ */
                            var google_conversion_id = 990978454;
                            var google_conversion_language = "en";
                            var google_conversion_format = "3";
                            var google_conversion_color = "ffffff";
                            var google_conversion_label = "Bd8OCIqdjQYQlsPE2AM";
                            var google_conversion_value = ' . $this->totals->value . ';
                            /* ]]> */
                            </script>
                            <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
                            </script>
                            <noscript>
                            <div style="display:inline;">
                            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/990978454/?value=20&amp;label=Bd8OCIqdjQYQlsPE2AM&amp;guid=ON&amp;script=0"/>
                            </div>
                            </noscript>';
        $this->tracking = $GoogleTracking;
    }
}

class cartItem {
    public $Type;
    public $Code;
    public $QTY;
    public $Price;
    public $Title;
    public $Description;
    public $Data;
    
    function calculateTax($VATable = TRUE){
        // Force a calculation of tax elements.
        // Assumings that $this->Price is the gross line price
        if($VATable) { $VAT = 1.2; } else { $VAT = 1; }
                
        $this->Price = number_format($this->Price,2);
        $this->grossunitprice = number_format(strval($this->Price) / $this->QTY,2);
        $this->netunitprice = number_format(strval($this->grossunitprice) / $VAT,2);      
        $this->unittax = number_format(strval($this->grossunitprice) - strval($this->netunitprice),2);
        $this->netlineprice = number_format(strval($this->netunitprice) * $this->QTY,2);
        $this->linetax = number_format(strval($this->unittax) * $this->QTY,2);
         
    }
    
}

class cartOrder {
    public $ID;
    private $e;
    
    public function __construct(&$e){
        $this->e =& $e;
    }
    
    public function saveHeader($data){
        // Create the header record
        $this->e->_db->delete("DELETE FROM trn_order_header WHERE ID = '{$this->ID}'");
        $this->e->_db->insert("INSERT INTO trn_order_header(ID,Status,Created) VALUES('{$this->ID}','checkout',NOW())");
        $requiredFields = array("CustomerEMail","BillingFirstnames","BillingSurname","BillingAddress1","BillingAddress2","BillingCity","BillingPostCode","BillingCountry",
                                "DeliveryFirstnames","DeliverySurname","DeliveryAddress1","DeliveryAddress2","DeliveryCity","DeliveryPostCode","DeliveryCountry");
        foreach($requiredFields as $requiredField){
            $value = @$data[$requiredField];
            $this->e->_db->update("UPDATE trn_order_header SET $requiredField = '$value' WHERE ID = '{$this->ID}'");
        }
    }
    
    public function saveProducts($data){
    	// Delete the lines that are already there for this order, in case we are updating the order.
        $this->e->_db->delete('DELETE FROM trn_order_lines WHERE ID = "' . $this->ID . '"');
        // Add in the product lines for this order.
        foreach($data as $cartThing){
            $insertSQL = 'INSERT INTO trn_order_lines SET ';
            $insertSQL .= "ID = '{$this->ID}'";
            // $insertSQL .= ",ItemID = '$cartThing->ID'";
            $insertSQL .= ",Code = '{$cartThing->Code}'";
            $insertSQL .= ",Name = '{$cartThing->Title}'";
            $insertSQL .= ",NetUnitPrice = {$cartThing->netunitprice}";
            $insertSQL .= ",UnitTax = {$cartThing->unittax}";
            $insertSQL .= ",GrossUnitPrice = {$cartThing->grossunitprice}";
            $insertSQL .= ",QTY = {$cartThing->QTY}";
            $insertSQL .= ",NetLinePrice = {$cartThing->netlineprice}";
            $insertSQL .= ",LineTax = {$cartThing->linetax}";
            $insertSQL .= ",GrossLinePrice = {$cartThing->Price}";
            $insertSQL .= ",Data = '{$cartThing->Data}'";
            $this->e->_db->insert($insertSQL);
        }
        // Add in the shipping for this order.
    }
       
    public function savePayment($paid,$method){
        if ($paid){
            // Grab and hold a paid status
            $SQL = "UPDATE trn_order_header SET 
                                   Paid = 1,
                                   PaymentTimestamp = NOW(), 
                                   PaymentReference = '$method'
                                   WHERE ID = '{$this->ID}'";
            
        } else {
            // Accept a failure only if we have not already had a paid status through
            // Stops Sage Pay from "double dipping"
            $SQL = "UPDATE trn_order_header SET Paid = -1 WHERE ID = '{$this->ID}' AND Paid = 0";
        }
        $this->e->_db->update($SQL);
    }
    
    public function saveCancellation(){
    	
    	// Grab and hold a paid status
		$SQL = "UPDATE trn_order_header SET
    				Paid = -1,
    				PaymentTimestamp = NOW(),
    				PaymentReference = '$method'
    				WHERE ID = '{$this->ID}'";
    	
    	 
    	$this->e->_db->update($SQL);
    }
    
    public function saveDispatch($tracking = ''){
    	 
    	// Grab and hold a paid status
    	$SQL = "UPDATE trn_order_header SET
    	Despatched = 1,
    	DespatchDate = NOW(),
    	DespatchTracking = '$tracking'
    	WHERE ID = '{$this->ID}'";
    	 
    	$this->e->_db->update($SQL);
    }
}

?>