<?php
//
// Created on: <14-May-2007 04:02:00 gb>
//
// Copyright (C) 2001-2007 Brookins Consulting. All rights reserved.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 or greater as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@brookinsconsulting.com if any conditions of
// this licencing isn't clear to you.
//

include_once( "lib/ezutils/classes/ezini.php" );

/*!
  \class GoogleAnalyticsOperators googleanalyticsoperators.php
  \brief Base documentation class for google analytics operators

  These operators provide for simple operators which may be installed into templates to provide google analytics client side javascript dependancies needed to enable eZ Publish to report pageview and webshop order statistics.

  Only the operators required to be installed into your pagelayout.tpl for page and order tracking are 'bc_ga_urchin', 'bc_ga_urchinHeader'.

  The rest of the dependancies are handled internaly.

  \Note This class is used by the googleanalytics extension
*/
class GoogleAnalyticsOperators
{
    /*!
     Constructor

    \Sets the class variable 'Operators' which contains an array of available operators names.
    \Sets the class variable 'Debug' to false.
    */
    function GoogleAnalyticsOperators()
    {
        $this->Operators = array( 'bc_ga_urchin', 'bc_ga_urchinHeader', 'bc_ga_urchinOrder', 'bc_ga_xmlAttributeValue', 'bc_ga_jsEscapedString', 'bc_ga_formatNumericDecimal' );
        $this->Debug = false;
    }

    /*!
     \Returns the operators provided in this class.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    /*!
     \Return true to tell the template engine that the parameter list
     exists per operator type, this is needed for operator classes
     that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     \ Returns an array of named parameters, this allows for easier retrieval
     of operator parameters. This also requires the function modify() has an extra
     parameter called $named_params.
 
     The position of each element (starts at 0) represents the position of the original
     sequenced parameters. The key of the element is used as parameter name, while the
     contents define the type and requirements.

     The keys of each element content is:
     * type - defines the type of parameter allowed
     * required - boolean which says if the parameter is required or not, if missing
     and required an error is displayed
     * default - the default value if the parameter is missing
    */
    function namedParameterList()
    {
        return array( 'bc_ga_urchin' => array( 'false' => array( 'type' => 'array', 'required' => false, 'default' => false )
                                             ),

                      'bc_ga_urchinHeader' => array( 'false' => array( 'type' => 'array', 'required' => false, 'default' => false )
                                             ),

                      'bc_ga_urchinOrder' => array( 'order' => array( 'type' => 'array', 'required' => true, 'default' => false )
                                             ),

                      'bc_ga_xmlAttributeValue' => array( 'name' => array( 'type' => 'string', 'required' => true, 'default' => false ),
                                                    'data' => array( 'type' => 'string', 'required' => true, 'default' => false )
                                             ),

                      'bc_ga_jsEscapedString' =>  array( 'string' => array( 'type' => 'string',
                                                   'required' => true,
                                                   'default' => '' )
                                              ),

                      'bc_ga_formatNumericDecimal' => array( 'number' => array( 'type' => 'string',
                                                       'required' => true,
                                                       'default' => '' ),
                                                       'places' => array( 'type' => 'string',
                                                       'required' => true,
                                                       'default' => '' )
                                              )
                      );
    }

    /*!
     \Executes the requested operator(s).
     \Checks operator names, and calls the appropriate functions and arguments.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                     &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'bc_ga_urchin':
            {
                $operatorValue = $this->bc_ga_urchin( );
            }
            break;

            case 'bc_ga_urchinHeader':
            {
                $operatorValue = $this->bc_ga_urchinHeader( );
            }
            break;

            case 'bc_ga_urchinOrder':
            {
                $operatorValue = $this->bc_ga_urchinOrder( $namedParameters['order'] );
            }
            break;

            case 'bc_ga_xmlAttributeValue':
            {
                $operatorValue = $this->bc_ga_xmlAttributeValue( $namedParameters['name'], $namedParameters['data'] );
            }
            break;

            case 'bc_ga_jsEscapedString':
            {
                $operatorValue = $this->bc_ga_jsEscapedString( $namedParameters['string'] );
            }
            break;

            case 'bc_ga_formatNumericDecimal':
            {
                $operatorValue = $this->bc_ga_formatNumericDecimal( $namedParameters['number'], $namedParameters['places'] );
            }
            break;
        }
    }

    /*!
     \Return bc_ga_urchinHeader html head javascript dependancies and javascript method call on the html body onload event.
     \Note This operator is required to be installed in the html head/body of your pagelayout.tpl by the bc_ga_urchinOrder operator.
    */
    function bc_ga_urchinHeader()
    {
        $ret = "</head>\n<body>";

        // Settings
        $ini =& eZINI::instance( 'googleanalytics.ini' );
        $submit = $ini->variable( 'GoogleAnalyticsWorkflow', 'OrderSubmitToGoogle');
        $uacct = $ini->variable( 'GoogleAnalyticsWorkflow', 'Urchin');

        // Checks
        if( $submit == 'enabled' and $uacct != 'disabled' )
        {
          // Add hook to detect the template override of pagelayout.tpl
          include_once( 'kernel/common/eztemplatedesignresource.php' );
          $res =& eZTemplateDesignResource::instance();

          // $res->setKeys( array( array( 'googleanalytics', '1' ) ) );
          $keys = $res->keys();

          // print_r( $keys );
          // if ( array_key_exists( 'section', $keys ) ) {

          if ( array_key_exists( 'googleanalytics', $keys ) ) {
              $ret = '<meta http-equiv="Content-Script-Type" content="text/javascript">'."\n".'</head>'."\n".'<body onload="__utmSetTrans();">';
          }
          else
          {
              $ret = "</head>\n<body>";
          }
        }
        else
        {
            $ret = "</head>\n<body>";
        }

        /*
        if ( $debug )
            ezDebug::writeNotice( $name, 'bc_ga_xmlAttributeValue:name' ); */

        return $ret;
    }

    /*!
     \Return bc_ga_urchin html javascript script dependancies and javascript method call with account settings.
     \Note The operator 'bc_ga_urchin' is implimented in the template override, pagelayout.tpl.
     \Note This operator is required to be installed in the html end body/html of your pagelayout.tpl template override.
    */
    function bc_ga_urchin()
    {
        $ret = false;

        // Settings
        $ini =& eZINI::instance( 'googleanalytics.ini' );

        $submit = $ini->variable( 'GoogleAnalyticsWorkflow', 'PageSubmitToGoogle');
        $uacct = $ini->variable( 'GoogleAnalyticsWorkflow', 'Urchin');
        $udn = $ini->variable( 'GoogleAnalyticsWorkflow', 'HostName');
        $script_url = $ini->variable( 'GoogleAnalyticsWorkflow', 'Script');

        // Checks
        if( $submit == 'enabled' and $uacct != 'disabled' and isset( $script_url ) )
        {
          $ret .= '<script src="'. "$script_url". '" type="text/javascript"> </script>'."\n";
          $ret .= '<script type="text/javascript">'."\n";
          // $ret .= '<!-- '."\n";
          $ret .= '  _uacct = "'. $uacct .'";'."\n";
          if ( $udn != 'disabled' )
          {
            $ret .= '  _udn = "'. $udn .'"'.";\n";
          }
          $ret .= "  bc_ga_urchin();\n";
          // $ret .= '--> '."\n";
          $ret .= '</script>';
        }

        /*
        if ( $debug )
            ezDebug::writeNotice( $name, 'bc_ga_xmlAttributeValue:name' ); */

        return $ret;
    }

    /*!
     \Return the string contents of an xml attribute.
     \Note The operator 'bc_ga_xmlAttributeValue' is implimented in the template override, order.tpl
     \Note Used to fetch the value of the xml attributes of the template variable containing the order's shopaccounthandler customer address information in xml.
    */
    function bc_ga_xmlAttributeValue( $name = false, $data, $ret = false, $debug = false )
    {
        // given string $data, will return the text string content of the $name attribute content of a given valid xml document.
        if ( $debug )
            ezDebug::writeNotice( $name, 'bc_ga_xmlAttributeValue:name' );

        // get information out of eZXML
        include_once('lib/ezxml/classes/ezxml.php');
        $xml = new eZXML();
        $xmlDoc = $data;

        // print_r($data);
        if ( $debug )
            ezDebug::writeNotice( $data, 'bc_ga_xmlAttributeValue:data' );

        // continue only with content
        if( $xmlDoc != null and $name != null )
        {
            $dom = $xml->domTree( $xmlDoc );
            $element = $dom->elementsByName( "$name" );

            // print_r ( $name );
            if ( isset( $element[0] ) )
            {
                $string = $element[0]->textContent();
            }
            else
            {
                // $string = null;
                $string = false;
            }
            $ret = $string;
        }

        if ( $debug )
            ezDebug::writeNotice( $ret, 'bc_ga_xmlAttributeValue:ret' );

        return $ret;
    }

    /*!
     \Return a string with htmlcharacters and other special characters escaped; a string safe for use by javascript.
     \Escaped Characters: , '
     \Note The operator 'bc_ga_jsEscapedString' is implimented in the template override, order.tpl
    */
    function bc_ga_jsEscapedString( $s )
    {
        $ret = false;
        if( $s )
        {
          // $t = addcslashes( $s, '\'\\"'."\n\r" );
          // $t = addcslashes( $s, '\'\\"'."\n\r" );
          $e = str_replace("'", "\'", $s);
          $e = str_replace('"', "'+String.fromCharCode(34)+'", $e);
          $ret = $e;
        }

        /* if ( $debug )
            ezDebug::writeNotice( $e, 'bc_ga_jsEscapedString:ret' ); */

        return $ret;
    }

    /*!
     \Return a number rounded with decimal place control
     \Note Used by order.tpl
    */
    function bc_ga_formatNumericDecimal( $n, $p=2 )
    {
        $ret = false;
        if( $n or $n == 0 )
        {
            // $r = round( $n, $p );
            $r = number_format($n, 2, '.', '');
            if( $r == '' or $r == 0 or $r == 0.00 )
            {
                $r = '0.00';
            }
            $ret = $r;
        }
        return $ret;
    }

    /*!
     \Return Bc_Ga_UrchinOrderTracker javascript method call with account settings
     \Note The operator bc_ga_urchinHeader is required by the bc_ga_urchinOrder operator.
     \Note The operator 'bc_ga_urchinOrder' is implimented in the template override, order.tpl
    */
    function bc_ga_urchinOrder( $order_id )
    {
        $ret = false;

        // Settings
        $ini =& eZINI::instance( 'googleanalytics.ini' );

        $submit = $ini->variable( 'GoogleAnalyticsWorkflow', 'PageSubmitToGoogle');
        $uacct = $ini->variable( 'GoogleAnalyticsWorkflow', 'Urchin');
        $udn = $ini->variable( 'GoogleAnalyticsWorkflow', 'HostName');
        $script_url = $ini->variable( 'GoogleAnalyticsWorkflow', 'Script');
        $shop_name = $ini->variable( 'GoogleAnalyticsWorkflow', 'ShopName');

        // Checks
        if( $submit == 'enabled' and $uacct != 'disabled' and isset( $script_url ) )
        {
            include_once( 'kernel/classes/ezorder.php' );

            $order_id = $order_id->ID;
            $order = new eZOrder( $order_id );
            // print_r( $order );

            $ret = '<form style="display:none;" name="utmform">';

            // Fetch Order Total
            $np_order_subtotal_price_ex_vat = $this->bc_ga_formatNumericDecimal( $order->totalExVAT() );
            $np_order_subtotal_price_inc_vat = $this->bc_ga_formatNumericDecimal( $order->totalIncVAT() );
            $np_order_subtotal_price_vat_sub = $np_order_subtotal_price_inc_vat - $np_order_subtotal_price_ex_vat;
            $np_order_subtotal_price_vat = $this->bc_ga_formatNumericDecimal( $np_order_subtotal_price_vat_sub );

            $ret .= '<textarea id="utmtrans">UTM:T|'."$order->OrderNr|$shop_name|$np_order_subtotal_price_ex_vat|$np_order_subtotal_price_vat|";

            // Fetch Shipping Total
            foreach( $order->orderItems() as $order_item )
            {
                $np_order_item_price_inc_vat = $this->bc_ga_formatNumericDecimal( $order_item->Price );
                $ret .= "$np_order_item_price_inc_vat";
            }

            $user_city = $this->bc_ga_xmlAttributeValue( 'city', $order->DataText1 );
            $user_state = $this->bc_ga_xmlAttributeValue( 'state', $order->DataText1 );
            $user_country = $this->bc_ga_xmlAttributeValue( 'country', $order->DataText1 );
            $ret .= "|$user_city|$user_state|$user_country\n";

            // Fetch Product Item Total
            foreach( $order->productItems() as $product_item )
            {
                $np_product_item_price_inc_vat = $this->bc_ga_formatNumericDecimal( $product_item['price_inc_vat'] );
                $product_item_node_id = $product_item['node_id'];
                $product_item_name = $product_item['object_name'];
                $product_item_count = $product_item['item_count'];

                // $product_item->item_object->contentobject->main_node->parent->name
                $item_object = $product_item['item_object'];
                $co = $item_object->contentObject();
                $main_node = $co->mainNode();
                $parent_co = $main_node->fetchParent();
                $product_item_parent_name = $parent_co->Name;

                $ret .= 'UTM:I|'."$order->OrderNr|$product_item_node_id|$product_item_name|$product_item_parent_name|$np_product_item_price_inc_vat|$product_item_count";
            }

            $ret .= '</textarea>';
            $ret .= '</form>';
        }

        /*
        if ( $debug )
            ezDebug::writeNotice( $ret, 'bc_ga_urchinOrder:ret' ); */

        return $ret;
    }

    /// \privatesection
    var $Operators;
    var $Debug;
}

?>
