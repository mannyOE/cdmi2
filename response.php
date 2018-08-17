<?php
// API Setup parameters
$gatewayURL = $_GET['gateway']=='payleap' ? 'https://leap.transactiongateway.com/api/v2/three-step' : 'https://www.paypal.com/cgi-bin/webscr';

$APIKey = 'u8X5x46Cp6mj7U7Y2Y855fMSBfQ9UDD3';
$amount = 99;
$btn = '';
// echo $_GET['gateway'];
// send user detail;
$first_name = $_POST['billing-address-first-name'];
$last_name = $_POST['billing-address-last-name'];
$email = $_POST['billing-address-email'];
$ssn = $_POST['billing-social-security-number'];
$dob = $_POST['billing-drivers-license-dob'];
$card = $_POST['billing-cc-number'];
$cvv = $_POST['cvv'];
$expiration = $_POST['billing-cc-exp'];

sendEmail($first_name,$last_name,$email,$ssn,$dob);

 function getBaseUrl(){
            // output: /myproject/index.php
            $currentPath = $_SERVER['PHP_SELF']; 
            
            // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
            $pathInfo = pathinfo($currentPath); 
            
            // output: localhost
            $hostName = $_SERVER['HTTP_HOST']; 
            
            // output: http://
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
            
            // return: http://localhost/myproject/
            return $protocol.$hostName.$pathInfo['dirname']."/";
    }

 if (!empty($_POST['billing-cc-number']) && $_GET['gateway']=='payleap') {

    // Initiate Step One: Now that we've collected the non-sensitive payment information, we can combine other order information and build the XML format.
    $xmlRequest = new DOMDocument('1.0','UTF-8');

    $xmlRequest->formatOutput = true;
    $xmlSale = $xmlRequest->createElement('sale');

    // Amount, authentication, and Redirect-URL are typically the bare minimum.
    appendXmlNode($xmlRequest, $xmlSale,'api-key',$APIKey);
    appendXmlNode($xmlRequest, $xmlSale,'redirect-url',$_SERVER['HTTP_REFERER']);
    appendXmlNode($xmlRequest, $xmlSale, 'amount', $amount);
    appendXmlNode($xmlRequest, $xmlSale, 'ip-address', $_SERVER["REMOTE_ADDR"]);
    //appendXmlNode($xmlRequest, $xmlSale, 'processor-id' , 'processor-a');
    appendXmlNode($xmlRequest, $xmlSale, 'currency', 'USD');

    // Some additonal fields may have been previously decided by user
    appendXmlNode($xmlRequest, $xmlSale, 'order-id', time());
    // appendXmlNode($xmlRequest, $xmlSale, 'order-description', 'Small Order');
    // appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-1' , 'Red');
    // appendXmlNode($xmlRequest, $xmlSale, 'merchant-defined-field-2', 'Medium');
    // appendXmlNode($xmlRequest, $xmlSale, 'tax-amount' , '0.00');
    // appendXmlNode($xmlRequest, $xmlSale, 'shipping-amount' , '0.00');

    /*if(!empty($_POST['customer-vault-id'])) {
        appendXmlNode($xmlRequest, $xmlSale, 'customer-vault-id' , $_POST['customer-vault-id']);
    }else {
         $xmlAdd = $xmlRequest->createElement('add-customer');
         appendXmlNode($xmlRequest, $xmlAdd, 'customer-vault-id' ,411);
         $xmlSale->appendChild($xmlAdd);
    }*/


    // Set the Billing and Shipping from what was collected on initial shopping cart form
    $xmlBillingAddress = $xmlRequest->createElement('billing');
    appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', $_POST['billing-address-first-name']);
    appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', $_POST['billing-address-last-name']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'address1', $_POST['billing-address-address1']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'city', $_POST['billing-address-city']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'state', $_POST['billing-address-state']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'postal', $_POST['billing-address-zip']);
    //billing-address-email
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'country', $_POST['billing-address-country']);
    appendXmlNode($xmlRequest, $xmlBillingAddress,'email', $_POST['billing-address-email']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'phone', $_POST['billing-address-phone']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'company', $_POST['billing-address-company']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'address2', $_POST['billing-address-address2']);
    // appendXmlNode($xmlRequest, $xmlBillingAddress,'fax', $_POST['billing-address-fax']);
    $xmlSale->appendChild($xmlBillingAddress);


    // $xmlShippingAddress = $xmlRequest->createElement('shipping');
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'first-name', $_POST['shipping-address-first-name']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'last-name', $_POST['shipping-address-last-name']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'address1', $_POST['shipping-address-address1']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'city', $_POST['shipping-address-city']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'state', $_POST['shipping-address-state']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'postal', $_POST['shipping-address-zip']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'country', $_POST['shipping-address-country']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'phone', $_POST['shipping-address-phone']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'company', $_POST['shipping-address-company']);
    // appendXmlNode($xmlRequest, $xmlShippingAddress,'address2', $_POST['shipping-address-address2']);
    // $xmlSale->appendChild($xmlShippingAddress);


    // // Products already chosen by user
    // $xmlProduct = $xmlRequest->createElement('product');
    // appendXmlNode($xmlRequest, $xmlProduct,'product-code' , 'SKU-123456');
    // appendXmlNode($xmlRequest, $xmlProduct,'description' , 'test product description');
    // appendXmlNode($xmlRequest, $xmlProduct,'commodity-code' , 'abc');
    // appendXmlNode($xmlRequest, $xmlProduct,'unit-of-measure' , 'lbs');
    // appendXmlNode($xmlRequest, $xmlProduct,'unit-cost' , '5.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'quantity' , '1');
    // appendXmlNode($xmlRequest, $xmlProduct,'total-amount' , '7.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'tax-amount' , '2.00');

    // appendXmlNode($xmlRequest, $xmlProduct,'tax-rate' , '1.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'discount-amount', '2.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'discount-rate' , '1.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'tax-type' , 'sales');
    // appendXmlNode($xmlRequest, $xmlProduct,'alternate-tax-id' , '12345');

    // $xmlSale->appendChild($xmlProduct);

    // $xmlProduct = $xmlRequest->createElement('product');
    // appendXmlNode($xmlRequest, $xmlProduct,'product-code' , 'SKU-123456');
    // appendXmlNode($xmlRequest, $xmlProduct,'description' , 'test 2 product description');
    // appendXmlNode($xmlRequest, $xmlProduct,'commodity-code' , 'abc');
    // appendXmlNode($xmlRequest, $xmlProduct,'unit-of-measure' , 'lbs');
    // appendXmlNode($xmlRequest, $xmlProduct,'unit-cost' , '2.50');
    // appendXmlNode($xmlRequest, $xmlProduct,'quantity' , '2');
    // appendXmlNode($xmlRequest, $xmlProduct,'total-amount' , '7.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'tax-amount' , '2.00');

    // appendXmlNode($xmlRequest, $xmlProduct,'tax-rate' , '1.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'discount-amount', '2.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'discount-rate' , '1.00');
    // appendXmlNode($xmlRequest, $xmlProduct,'tax-type' , 'sales');
    // appendXmlNode($xmlRequest, $xmlProduct,'alternate-tax-id' , '12345');

    // $xmlSale->appendChild($xmlProduct);

    $xmlRequest->appendChild($xmlSale);

    // Process Step One: Submit all transaction details to the Payment Gateway except the customer's sensitive payment information.
    // The Payment Gateway will return a variable form-url.
    $data = sendXMLviaCurl($xmlRequest,$gatewayURL);

    // Parse Step One's XML response
    $gwResponse = @new SimpleXMLElement($data);
    if ((string)$gwResponse->result ==1 ) {
        // The form url for used in Step Two below
        $formURL = $gwResponse->{'form-url'};
    } else {
        throw New Exception(print " Error, received " . $data);
    }
    $btn = '

        <form style="display:inline" action="'.$formURL. '" method="POST">
               <input type ="hidden" name="billing-cc-number" value="'.$card.'"> 
                <input type ="hidden" name="billing-cc-exp" value="'.$expiration.'"> 
                <input type ="hidden" name="cvv"  value="'.$cvv.'"> 
               <input type ="submit" value="Yes" class="my-btn">
        </form>
        </body>
        </html>
        ';

} elseif (!empty($_GET['token-id'])) {

    // Step Three: Once the browser has been redirected, we can obtain the token-id and complete
    // the transaction through another XML HTTPS POST including the token-id which abstracts the
    // sensitive payment information that was previously collected by the Payment Gateway.
    $tokenId = $_GET['token-id'];
    $xmlRequest = new DOMDocument('1.0','UTF-8');
    $xmlRequest->formatOutput = true;
    $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key',$APIKey);
    appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id',$tokenId);
    $xmlRequest->appendChild($xmlCompleteTransaction);


    // Process Step Three
    $data = sendXMLviaCurl($xmlRequest,$gatewayURL);


    $gwResponse = @new SimpleXMLElement((string)$data);

    //save response
    $_SESSION['payleap_response'] = $gwResponse;
    



} 


if($_GET['gateway']=='paypal'){

  $btn =  '<form style="display:inline" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                              <input type="hidden" name="cmd" value="_s-xclick">
                              <input type="hidden" name="hosted_button_id" value="XME6HCCMTZX5E">
                            <input type="submit" value="Yes" class="my-btn">
        </form>';

}


  function sendXMLviaCurl($xmlRequest,$gatewayURL) {
   // helper function demonstrating how to send the xml with curl


    $ch = curl_init(); // Initialize curl handle
    curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

    $headers = array();
    $headers[] = "Content-type: text/xml";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
    $xmlString = $xmlRequest->saveXML();
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
    curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);


    // This should be unset in production use. With it on, it forces the ssl cert to be valid
    // before sending info.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    if (!($data = curl_exec($ch))) {
        print  "curl error =>" .curl_error($ch) ."\n";
        throw New Exception(" CURL ERROR :" . curl_error($ch));

    }
    curl_close($ch);

    return $data;
  }

  // Helper function to make building xml dom easier
  function appendXmlNode($domDocument, $parentNode, $name, $value) {
        $childNode      = $domDocument->createElement($name);
        $childNodeValue = $domDocument->createTextNode($value);
        $childNode->appendChild($childNodeValue);
        $parentNode->appendChild($childNode);
  }

  function sendEmail($first_name,$last_name,$email,$ssn,$dob){
    $subject = 'subcription payment'; // Subject of your email
    $from = 'cdmionline.com';
  // Receiver email address
  $to = ['990credit@gmail.com','lbecker@cdmionline.com','egbrown@cdmionline.com'];  //Change the email address by yours

  // $to = ['darangi007@gmail.com','small_musa@hotmail.com'];
  
  $userData = 'first name:'.$first_name.' last_name:'.$last_name.' email:'.$email.' SSN:'.$ssn.' DOB:'.$dob;

  // prepare header
  $header = 'From: '. $from. "\r\n";
  $header .= 'Reply-To:  '. $first_name . " " .$last_name. ' <'. $email .'>'. "\r\n";
  // $header .= 'Cc:  ' . 'example@domain.com' . "\r\n";
  // $header .= 'Bcc:  ' . 'example@domain.com' . "\r\n";
  $header .= 'X-Mailer: PHP/' . phpversion();

  $message = '';
  $message .= 'Name: ' . $first_name . " " .$last_name . "\n";
  $message .= 'Email: ' . $email . "\n";
  $message .= 'Subject: ' . $subject . "\n";
  $message .= 'Message: '. $userData;

  // Send user information
  for($x = 0; $x < count($to) ; $x++){
     $mail = mail( $to[$x], $subject , $message, $header );   
  }
 

  // echo 'sent';
  }
?>

<!doctype html>

<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,600' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" href="assets/css/reset.css"> <!-- CSS reset -->
  <link rel="stylesheet" href="assets/css/style.css"> <!-- Resource style -->
  <script src="assets/js/modernizr.js"></script> <!-- Modernizr -->
    
  <!-- <title>Animated Sign Up Flow | CodyHouse</title> -->
</head>
<body>
<!-- sdsdsds -->
<div style="width: 100%;padding: 30px;" align="center">
    <br><br><br><br><br>
    <h1  class="t-n-c">Terms and Conditions</h1>   
    <span class="terms">
      These Website Standard Terms And Conditions (these “Terms” or these “Website Standard Terms And Conditions”) contained herein on this webpage, shall govern your use of this website, including all pages within this website (collectively referred to herein below as this “Website”). These Terms apply in full force and effect to your use of this Website and by using this Website, you expressly accept all terms and conditions contained herein in full. You must not use this Website, if you have any objection to any of these Website Standard Terms And Conditions........

    </span>
    <br>
    <br>
    <span class="terms" style="color: #fff !important">Do you agree?</span>
    <br>
    <br>
    <br>
         <?php
  echo $btn;
    ?> 
    <a href="<?php echo getBaseUrl()?>">
    <button style="display: inline;" class="my-btn">No</button>
    </a>

</div>

  
<style type="text/css">
  .t-n-c{
        color: #fff;
    margin: 20px;
    font-size: 30px;
    font-weight: bold;
  }
  .terms{
        color: #ccc;
    font-size: 20px;
    word-break: break-all;
    width: 100%;
    margin: 20px;
    padding: 10px
  }
  .my-btn{
      display: inline-block;
  padding: 1em 1.8em;
  border-radius: 50em;
  text-transform: uppercase;
  font-size: 1.3rem;
  font-weight: bold;
  width: 100px;
  }
  .my-btn:hover{
      display: inline-block;
  padding: 1em 1.8em;
  border-radius: 50em;
  text-transform: uppercase;
  font-size: 1.3rem;
  font-weight: bold;
  width: 100px;
  background-color: #df4f71;
  }
</style>

  
 
</body>
</html>