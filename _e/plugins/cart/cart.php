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
        $this->totals = new stdClass();
        $this->totals->items = 0;
        $this->totals->value = '0.00';
        $this->items = array();
        $this->services = array();
        $this->tracking = '';
        $this->_go();
    }
    
    private function _go(){
        $this->loadCart();
        $this->addToCart();
        $this->saveCart();
        $this->totalCart();
        print_r($this->e->_messaging->messages);
    } 
    
    public function go(&$k){
        // Keep a track on K, we are going to need it in all our subroutines
        $this->k =& $k;
        // This plug in might load even when we have no database
        // Check for DB connectivity before we start.
        if ($k->__db->hasDB()){
            // Check for shipping flags
            if (isset($_REQUEST['upgradeshipping'])){
                $_SESSION['cart-shipping-upgraded'] = TRUE;
                $this->k->__messaging->addMessage("You have been upgraded to priority shipping");
            }
            if (isset($_REQUEST['downgradeshipping'])){
                unset($_SESSION['cart-shipping-upgraded']);
                $this->k->__messaging->addMessage("You are now using standard shipping");
            }
                        
            if (isset($_REQUEST['addToCartItem']) 
                OR isset($_REQUEST['updateCartItem']) 
                OR isset($_REQUEST['cartDiscountCode'])
                OR isset($_REQUEST['upgradeshipping'])
                OR isset($_REQUEST['downgradeshipping'])){
            
                // Load the users current cart
                $this->loadCart();
                $this->totalCart();
                
                // Clear out services, something has changed
                $this->clearServices();
                
                if (isset($_REQUEST['addToCartItem']) OR isset($_REQUEST['updateCartItem'])){
                    // Add items to the cart
                    $this->addToCart();
                    // Update items in the cart
                    $this->updateCart();
                
                    // Save and reload cart to ensure correct pricing is in place.
                    $this->saveCart();
                    $this->loadCart();
                    $this->totalCart();
                }

                // Apply services (such as automatically selecting shipping)
                $this->applyServices();
                // Total up the cart again, after shipping
                $this->totalCart();
                
                // Apply any discount codes to the cart
                /*
                if (isset($_REQUEST['addToCartItem']) OR isset($_REQUEST['updateCartItem'])){
                    // If we change the contents of the basket, we lose our existing discount code
                    // UNLESS: We have passed in a discount code with our order.
                    if (isset($_SESSION['cart-discount-code']) && !isset($_REQUEST['cartDiscountCode'])){
                        $this->k->__messaging->addMessage("As the contents of your order have changed, we had to remove your discount code to make sure you get the right amount of discount.<br>Please enter it again to see how much you can save.");  
                    }
                    unset($_SESSION['cart-discount-code']);
                }
                 */
                
                $this->applyDiscountCodes();
                
                // Save the cart
                $this->saveCart();
            }
            
            // Load & total up the cart again, after discounts, or just after a load
            $this->loadCart();
            $this->totalCart();
            
        }
        
    }
    
    private function loadCart(){
        // Clear the current items array
        $this->items = array();
        
        $cartSQL = 'SELECT ID,QTY,Data FROM trn_cart WHERE session_id = "' . session_id() . '"';
        $cartData = $this->e->_db->select($cartSQL);
        
        while($cartRow = mysql_fetch_assoc($cartData)){
            if (strlen($cartRow['ID']) > 0) {
                $this->items[$cartRow['ID']] = new cartItem($cartRow['ID'],$cartRow['QTY'],$this->e); 
            }
        }
        
        $serviceSQL = 'SELECT * FROM trn_cart_services WHERE session_id = "' . session_id() . '"';
        $servicesData = $this->e->_db->select($serviceSQL);
        
        while($serviceRow = mysql_fetch_assoc($servicesData)){
            $service = new cartService();
            $service->ID = $serviceRow['ID'];
            $service->Type = $serviceRow['Type'];
            $service->Code = $serviceRow['Code'];
            $service->QTY = $serviceRow['QTY'];
            $service->Price = $serviceRow['Price'];
            $service->Title = $serviceRow['Title'];
            $service->Description = $serviceRow['Description'];
            $service->Data = $serviceRow['Data'];
            
            $this->services[$serviceRow['ID']] = $service; 
        }
        
    }
    
    private function clearServices(){
        $this->services = array();
    }
    
    private function addToCart(){
        $addToCartItem = 0;
        $addToCartQty = 1;
        
        if (isset($_REQUEST['addToCartItem'])){ $addToCartItem = $_REQUEST['addToCartItem']; }
        if (isset($_REQUEST['addToCartQty'])){ $addToCartQty = $_REQUEST['addToCartQty']; } else { $addToCartQty = 1; }
        
        if ($addToCartItem && $addToCartQty > 0) {
            if (!isset($this->items[$addToCartItem])){
                $cartThing = new cartItem($addToCartItem,0,$this->e);
            } else {
                $cartThing = $this->items[$addToCartItem];
            }
            print "{$cartThing->QTY} + $addToCartQty = ";
            $cartThing->QTY += $addToCartQty;
            print $cartThing->QTY;
            
            $this->items[$addToCartItem] = $cartThing;
            $this->e->_messaging->addMessage("{$cartThing->QTY} {$cartThing->item->content->xml->title} has been added to your cart.");
        }
        
    }
    
    private function updateCart(){
        $updateCartItem = 0;
        $updateCartQty = 1;
        
        if (isset($_REQUEST['updateCartItem'])){ $updateCartItem = $_REQUEST['updateCartItem']; }
        if (isset($_REQUEST['updateCartQty'])){ $updateCartQty = $_REQUEST['updateCartQty']; }
        
        if ($updateCartItem > 0) {
            if (!isset($this->things[$updateCartItem])){
                $cartThing = new cartThing($updateCartItem,0,$this->k);
            } else {
                $cartThing = $this->things[$updateCartItem];
            }
            if ($updateCartQty == 0){
                if (isset($this->things[$updateCartItem])){
                    $cartThing = $this->things[$updateCartItem];
                    $this->k->__messaging->addMessage("{$cartThing->thing->xml->title} has been removed from your cart.");
                    unset($this->things[$updateCartItem]);
                }
            } else {
                $cartThing->QTY = $updateCartQty;
                $this->things[$updateCartItem] = $cartThing;
                $this->k->__messaging->addMessage("{$cartThing->thing->xml->title} has been updated in your cart.");
            }
        }
    }
    
    private function applyServices(){
        // Apply our default shipping matrix
        $this->totalCart();
        if ($this->totals->items >= 1){
            if ($this->totals->items == 1) {
                if ($this->totals->value < 100){
                    if (isset($_SESSION['cart-shipping-upgraded'])){
                        $shippingCost = 8.55;
                    } else {
                        $shippingCost = 5.00;
                    }
                } else {
                    if (isset($_SESSION['cart-shipping-upgraded'])){
                        $shippingCost = 10.75;
                    } else {
                        $shippingCost = 6.30;
                    }
                }
            } else {
                if (isset($_SESSION['cart-shipping-upgraded'])){
                    $shippingCost = 10.75;
                } else {
                    $shippingCost = 6.30;
                }
            }
                
            unset($this->services[-1]);
            $shipping = new cartService();
            $shipping->ID = -1;
            $shipping->Type = 'SHIPPING';
            $shipping->Title = 'Shipping and Handling';
            $shipping->QTY = 1;
            $shipping->Price = $shippingCost;
            
            if (isset($_SESSION['cart-shipping-upgraded'])){
                $shipping->Code = 'PRIORITY';
                $day = date('w');
                if ($day == 0 || $day == 5 || $day == 6){
                    $shipping->Description = "Priority Delivery. Tuesday before 1pm
                        <br><a href='/cart?downgradeshipping'>No rush? Click here to save money with standard delivery</a>";
                } else {
                    $hour = date('H');
                    if($hour <= 11){
                        $shipping->Description = "Priority Delivery. Tomorrow before 1pm
                            <br><a href='/cart?downgradeshipping'>No rush? Click here to save money with standard delivery</a>";
                    } else {
                        $day = date('l',strtotime('+2 days'));
                        $shipping->Description = "Priority Delivery. $day before 1pm
                            <br><a href='/cart?downgradeshipping'>No rush? Click here to save money with standard delivery</a>";
                    }
                }
            } else {
                $shipping->Code = 'STANDARD';
                $shipping->Description = "Standard Delivery, 1-2 working days.";
                // $shipping->Description .= "<br><a href='/cart?upgradeshipping'>In a hurry? Click here to upgrade to priority delivery</a>";
            }
                        
            $this->services[-1] = $shipping;
        } else {
            unset($this->services[-1]);
        }
    }
    
    private function applyDiscountCodes(){
        if (!isset($_REQUEST['cartDiscountCode']) && isset($_SESSION['cart-discount-code'])){
            // Remember the code we entered before.
            $_REQUEST['cartDiscountCode'] = $_SESSION['cart-discount-code'];
        }
        
        if (isset($_REQUEST['cartDiscountCode'])){
            // We are changing our discount code. Delete any existing discount code.
            unset($this->services[-2]);
            // Treat all codes as uppercase
            $_REQUEST['cartDiscountCode'] = strtoupper($_REQUEST['cartDiscountCode']);
            $_SESSION['cart-discount-code'] = $_REQUEST['cartDiscountCode'];
            // Read totals
            
            $discountAmount = 0;
            $discountDescription = '';
            
            switch ($_REQUEST['cartDiscountCode']){
                case 'CENTRIC10':
                    $discountAmount = 0.1;
                    $discountDescription = '10% off your order, with thanks from eCommerce Centric!';
                    break;
                case 'TECHFF':
                    $discountAmount = 0.1;
                    $discountDescription = '10% off your order, with thanks from Technology Centric!';
                    break;
                case 'TECHBFF':
                    $discountAmount = 0.2;
                    $discountDescription = '20% off your order, with thanks from Technology Centric!';
                    break;
                case 'VIRGIN':
                    $discountAmount = 0.1;
                    $discountDescription = '10% off your order, with thanks from Virgin &amp; Technology Centric!';
                    break;
                case 'WELCOMEBACK':
                    $discountAmount = 0.1;
                    $discountDescription = 'Thanks for coming back. Here\'s 10% off your order, with thanks again from Technology Centric!';
                    break;
                case 'SORRY':
                    $discountAmount = 0.05;
                    $discountDescription = 'Thanks for giving us another chance. Here\'s 5% off your order!';
                    break;
                case 'FREESHIP':
                case 'HAPPYNEWYEAR':
                case 'ESHOT2':
                    if (isset($this->services[-1])){
                        $discountAmount = $this->services[-1]->Price;
                        $discountDescription = 'Free Shipping, with thanks from Technology Centric';
                    }
                    break;
                case 'ESHOT1':
                    $discountAmount = 0;
                    if(isset($this->things[10])){$discountAmount += $this->things[10]->QTY * 30;}
                    if(isset($this->things[3])){$discountAmount += $this->things[3]->QTY * 20;}
                    if(isset($this->things[4])){$discountAmount += $this->things[4]->QTY * 10;}
                    if(isset($this->things[5])){$discountAmount += $this->things[5]->QTY * 20;}
                    $discountDescription = 'Your exclusive deal as a recipient of our eShot';
                case '8INCH':
                    $discountAmount = 0;
                    if(isset($this->things[27])){
                        $discountAmount += $this->things[27]->QTY * 16;
                        if (isset($this->services[-1])){
                            $discountAmount += $this->services[-1]->Price;
                        }
                    }
                    $discountDescription = '10% off the R83.3 &amp; Free Shipping, with thanks from Technology Centric';
                    break;
                case '9INCH':
                    $discountAmount = 0;
                    if(isset($this->things[16])){
                        $discountAmount += $this->things[16]->QTY * 20;
                    }
                    $discountDescription = '£20 off the R974, with compliments from Technology Centric';
                    break;
                case 'FIVER':
                    $discountAmount = 5;
                    $discountDescription = 'Five pound complimentary voucher';
                    break;
                case 'TENNER':
                    $discountAmount = 10;
                    $discountDescription = 'Ten pound complimentary voucher';
                    break;
            }
            
            // Check to see if this is a valid code
            if ($discountAmount > 0){
                $discount = new cartService();
                $discount->ID = -2;
                $discount->Type = 'DISCOUNT';
                $discount->Code = $_REQUEST['cartDiscountCode'];
                $discount->QTY = 1;
                if ($discountAmount < 1){
                    $discount->Price = round($this->totals->value * $discountAmount,2) * -1;
                } else {
                    $discount->Price = number_format($discountAmount * -1,2);
                }
                $discount->Title = 'Technology Centric ' . $_REQUEST['cartDiscountCode'] . ' Discount';
                $discount->Description = $discountDescription;
                // $discount->Image = '_templates/HTML/eCommerce/images/orange_star_small.png';
                $discount->QTY = 1;
            }
                
            if (isset($discount)){
                if($discount->Price <> 0){
                    $this->k->__messaging->addMessage('Your discount code ' . $discount->Code . ' has been accepted and added to your order for a discount of £' . number_format(ABS($discount->Price),2));
                } else {
                    $this->k->__messaging->addMessage('Your discount code ' . $discount->Code . ' has been accepted, please add some products to your cart to see what you can save');
                }
                $this->services[-2] = $discount;
            } else {
                if ($discountAmount == -1){
                    if ($_REQUEST['cartDiscountCode'] == 'ESHOT1'){
                        $this->k->__messaging->addMessage('Thanks for entering our ESHOT1 code. As the items on offer have been further reduced in our sale, this code is no longer valid.');
                    } else {
                        $this->k->__messaging->addMessage('Sorry, but that discount code cannot be used on items that are already on sale.');
                    }
                } else {
                    $this->k->__messaging->addMessage('Sorry. You have entered an expired or invalid discount code');
                    unset($_SESSION['cart-discount-code']);
                }
            }
            
        } else {
            if(isset($this->services[-2]) && ($this->totals->items > 0)){
                $this->k->__messaging->addMessage('The contents of your basket have changed. To make sure you are still eligible for any discounts, please re-enter your discount code.');
            }
            unset($this->services[-2]);
        }
    }
    
    public function totalCart(){
        $this->totals->items = 0;
        $this->totals->value = 0.00;
        foreach($this->items as $cartthing){
            $this->totals->items += $cartthing->QTY;
            $this->totals->value += $cartthing->Price;
        }
        // Before we total the services, sort them out so that they are in order.
        foreach($this->services as $cartService){
            $this->services[$cartService->ID]->calculateTax();
            $this->totals->value += $cartService->Price;
        }
    }
    
    private function saveCart(){
        $this->e->_db->delete('DELETE FROM trn_cart WHERE session_id = "' . session_id() . '"');
        foreach($this->items as $cartThing){
            if ($cartThing->QTY > 0){
                $this->e->_db->insert(sprintf('INSERT INTO trn_cart SET session_id = "%s",ID = "%s",QTY = %d',  session_id(),$cartThing->ID,$cartThing->QTY));
            }            
        }
        $this->e->_db->delete('DELETE FROM trn_cart_services WHERE session_id = "' . session_id() . '"');
        foreach($this->services as $cartService){
            if ($cartService->QTY > 0){
                $this->e->_db->insert(sprintf('INSERT INTO trn_cart_services SET session_id = "%s",ID = %d,Type = "%s",Code = "%s",QTY = %d, Price = %f, Title = "%s", Description = "%s", Data = "%s"',  session_id(),$cartService->ID,$cartService->Type,$cartService->Code,$cartService->QTY,$cartService->Price,$cartService->Title,$cartService->Description,$cartService->Data));
            }            
        }
        return TRUE;
    }
    
    public function emptyCart(){
        $this->things = array();
        $this->clearServices();
        $this->saveCart();
        $this->totalCart();
    }
    
    public function createOrder(){
        // Order the contents of the cart.
        $this->order = new cartOrder($this->k);
        $this->order->ID = uniqid();
        $this->order->saveHeader($_POST);
        $this->order->saveProducts($this->things);
        $this->order->saveServices($this->services);
    }
    
    public function checkout_amazon(){        
        // Build the checkout HTML code for an Amazon checkout.
        $return = new \stdClass();
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/Cart.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/Item.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/MandatoryProperties.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/OptionalProperties.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/Price.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/Promotion.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/ShippingMethod.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/ShippingRate.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Model/Weight.php');
        include_once('_k/lib/amazonCheckout/src/AmazonPayments/Signature/SignatureCalculator.php');
        
        // Construct the cart
        $AMZcart = new \AmazonPayments\Model\Cart('_k/lib/amazonCheckout/src/AmazonPayments/cba_config.ini');
        
        // Build our shipping method
        if ($this->totals->items == 1 && $this->totals->value <= 100) {
            $shippingrateSTDCost = 5.00;
            $shippingrateNDYCost = 8.55;
        } else {
            $shippingrateSTDCost = 6.30;
            $shippingrateNDYCost = 10.75;
        }
            
        $shippingrateSTD = new \AmazonPayments\Model\ShippingRate();
        $shippingrateSTD->setShipmentBasedRate($shippingrateSTDCost);
        
        $shippingMethodSTD = new \AmazonPayments\Model\ShippingMethod(
                                \AmazonPayments\Model\ShippingMethod::REGION_CUSTOM,
                                \AmazonPayments\Model\ShippingMethod::SERVICE_STANDARD,
                                'Royal Mail Recorded Delivery',$shippingrateSTD);
        $shippingMethodSTD->includeCustomCountry("GB");
        
        $shippingrateNDY = new \AmazonPayments\Model\ShippingRate();
        $shippingrateNDY->setShipmentBasedRate($shippingrateNDYCost);
        
        $shippingMethodNDY = new \AmazonPayments\Model\ShippingMethod(
                                \AmazonPayments\Model\ShippingMethod::REGION_CUSTOM,
                                \AmazonPayments\Model\ShippingMethod::SERVICE_EXPEDITED,
                                'Royal Mail Special Delivery',$shippingrateNDY);
        $shippingMethodNDY->includeCustomCountry("GB");
        
        // Add items to the cart
        foreach($this->things as $cartItem){
            if ($cartItem->ID > 0 ){
                $AMZItem = new \AmazonPayments\Model\Item($cartItem->thing->product->sku,
                                                       $cartItem->thing->xml->title,
                                                       $cartItem->thing->product->price->_default->sell->value,
                                                       $cartItem->QTY);
                $AMZItem->setWeight(0.00);
                $AMZItem->setDescription((string)$cartItem->thing->xml->content->teaser);

                // Add in the shipping methods
                $AMZItem->setFulfillmentMethod(\AmazonPayments\Model\Item::FULFILLED_BY_MERCHANT);
                $AMZItem->addShippingMethod($shippingMethodSTD);
                $AMZItem->addShippingMethod($shippingMethodNDY);
                
                $AMZcart->addItem($AMZItem);
            } else {
                // Add in promotion (Note: Only one per item)
                // $AMZItem->setPromotion(floatval(ABS($cartItem->Product->SellPrice)), $cartItem->Teaser);
            }
            
        }
        
        /* Sandbox: <script type="text/javascript" src="https://static-eu.payments-amazon.com/cba/js/gb/sandbox/PaymentWidgets.js"></script> 
         * <img src="https://payments-sandbox.amazon.co.uk/gp/cba/button?type=cart&cartOwnerId=AJ8F0WGPZE618&color=tan&size=x-large&background=light"/> 
         * 
         * REMOVE "sandbox" from these to go live. Clever. Not.
         */
        
        $return = '<script type="text/javascript" src="https://static-eu.payments-amazon.com/cba/js/gb/sandbox/PaymentWidgets.js"></script>
                            <div id="cbaButton1">      
                                <img src="https://payments-sandbox.amazon.co.uk/gp/cba/button?type=cart&cartOwnerId=AJ8F0WGPZE618&color=tan&size=x-large&background=light"/> 
                            </div>
                            <script type="text/javascript">
                            new CBA.Widgets.StandardCheckoutWidget({
                                merchantId:"AJ8F0WGPZE618",
                                orderInput: { 
                                    format: "XML",
                                    value: "' . $AMZcart->createOrderInputValue() . '"},
                                buttonSettings: { size: "x-large",color:"tan",background:"light"}
                            }).render("cbaButton1");
                            </script>';
        
        return $return;
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
    
    public $ID;
    public $QTY;
    public $Price;
    public $item;
    
    function __construct($itemPath,$QTY,$e){
        $item = e::_search($itemPath);
        if (sizeof($item) == 1){ $item = array_shift($item);}
        if (is_object($item)){
            $this->ID = $itemPath;
            $this->item = $item;
            $this->QTY = $QTY;
            $this->grossunitprice = number_format(strval($this->item->content->product->price->_default->sell->value),2);
            $this->netunitprice = number_format(strval($this->grossunitprice) / 1.2,2);      
            $this->unittax = number_format(strval($this->grossunitprice) - strval($this->netunitprice),2);
            $this->netlineprice = number_format(strval($this->netunitprice) * $this->QTY,2);
            $this->linetax = number_format(strval($this->unittax) * $this->QTY,2);

            $this->Price = number_format(strval($this->item->content->product->price->_default->sell->value) * $this->QTY,2);
        } else {
            // Problem loading the item
            $e->_messaging->addMessage('One or more of the items in your basket could not be loaded.',-1);
        }
        
    }
    
}

class cartService {
    public $ID;
    public $Type;
    public $Code;
    public $QTY;
    public $Price;
    public $Title;
    public $Description;
    public $Data;
    
    /*
    function __construct($ServiceID,$Code,$QTY,$Price,$Description){
        $this->ID = $ID;
        $this->Code = $Code;
        $this->QTY = $QTY;
        $this->Price = $Price;
        $this->Description = $Description;
    }
    */
    
    function calculateTax(){
        // Force a calculation of tax elements.
        // Assumings that $this->Price is the gross line price
        $this->Price = number_format($this->Price,2);
        $this->grossunitprice = number_format(strval($this->Price) / $this->QTY,2);
        $this->netunitprice = number_format(strval($this->grossunitprice) / 1.2,2);      
        $this->unittax = number_format(strval($this->grossunitprice) - strval($this->netunitprice),2);
        $this->netlineprice = number_format(strval($this->netunitprice) * $this->QTY,2);
        $this->linetax = number_format(strval($this->unittax) * $this->QTY,2);
         
    }
    
}

class cartOrder {
    public $ID;
    private $k;
    
    public function __construct(&$k){
        $this->k =& $k;
    }
    
    public function saveHeader($data){
        // Create the header record
        $this->k->__db->delete("DELETE FROM trn_order_header WHERE ID = '{$this->ID}'");
        $this->k->__db->insert("INSERT INTO trn_order_header(ID,Status,Created) VALUES('{$this->ID}','checkout',NOW())");
        $requiredFields = array("CustomerEMail","BillingFirstnames","BillingSurname","BillingAddress1","BillingAddress2","BillingCity","BillingPostCode","BillingCountry",
                                "DeliveryFirstnames","DeliverySurname","DeliveryAddress1","DeliveryAddress2","DeliveryCity","DeliveryPostCode","DeliveryCountry");
        foreach($requiredFields as $requiredField){
            $value = @$data[$requiredField];
            $this->k->__db->update("UPDATE trn_order_header SET $requiredField = '$value' WHERE ID = '{$this->ID}'");
        }
    }
    
    public function saveProducts($data){
        $this->k->__db->delete('DELETE FROM trn_order_lines WHERE ID = "' . $this->ID . '"');
        foreach($data as $cartThing){
            $insertSQL = 'INSERT INTO trn_order_lines SET ';
            $insertSQL .= "ID = '{$this->ID}'";
            $insertSQL .= ",ItemID = $cartThing->ID";
            $insertSQL .= ",Code = '{$cartThing->thing->product->sku}'";
            $insertSQL .= ",Name = '{$cartThing->thing->xml->title}'";
            $insertSQL .= ",NetUnitPrice = {$cartThing->netunitprice}";
            $insertSQL .= ",UnitTax = {$cartThing->unittax}";
            $insertSQL .= ",GrossUnitPrice = {$cartThing->grossunitprice}";
            $insertSQL .= ",QTY = {$cartThing->QTY}";
            $insertSQL .= ",NetLinePrice = {$cartThing->netlineprice}";
            $insertSQL .= ",LineTax = {$cartThing->linetax}";
            $insertSQL .= ",GrossLinePrice = {$cartThing->Price}";
            $insertSQL .= ",Data = ''";
            $this->k->__db->insert($insertSQL);
        }
    }
    
    public function saveServices($data){
        foreach($data as $cartThing){
            $insertSQL = 'INSERT INTO trn_order_lines SET ';
            $insertSQL .= "ID = '{$this->ID}'";
            $insertSQL .= ",ItemID = $cartThing->ID";
            $insertSQL .= ",Code = '{$cartThing->Code}'";
            $insertSQL .= ",Name = '{$cartThing->Title}'";
            $insertSQL .= ",NetUnitPrice = {$cartThing->netunitprice}";
            $insertSQL .= ",UnitTax = {$cartThing->unittax}";
            $insertSQL .= ",GrossUnitPrice = {$cartThing->grossunitprice}";
            $insertSQL .= ",QTY = {$cartThing->QTY}";
            $insertSQL .= ",NetLinePrice = {$cartThing->netlineprice}";
            $insertSQL .= ",LineTax = {$cartThing->linetax}";
            $insertSQL .= ",GrossLinePrice = {$cartThing->Price}";
            $insertSQL .= ",Data = ''";
            $this->k->__db->insert($insertSQL);
        }
    }
    
    public function savePayment($paid,$paymentresponse){
        if ($paid){
            // Grab and hold a paid status
            $this->k->__db->update("UPDATE trn_order_header SET 
                                    Paid = 1,
                                    PaymentTimestamp = NOW(), 
                                    PaymentReference = '{$paymentresponse->TxAuthNo}'
                                    WHERE ID = '{$this->ID}'");
        } else {
            // Accept a failure only if we have not already had a paid status through
            // Stops Sage Pay from "double dipping"
            $this->k->__db->update("UPDATE trn_order_header SET Paid = -1 WHERE ID = '{$this->ID}' AND Paid = 0");
        }
    }
}




?>