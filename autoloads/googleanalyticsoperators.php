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

class GoogleAnalyticsOperators
{
    /*!
     Constructor
    */
    function GoogleAnalyticsOperators()
    {
        $this->Operators = array( 'urchinTracker', 'urchinTrackerHeader', 'urchinOrderTracker', 'xmlAttributeValue', 'jsEscapedString', 'formatNumericDecimal' );
        $this->Debug = false;
    }

    /*!
     Returns the operators in this class.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'urchinTracker' => array( 'false' => array( 'type' => 'array', 'required' => false, 'default' => false )
                                             ),

                      'urchinTrackerHeader' => array( 'false' => array( 'type' => 'array', 'required' => false, 'default' => false )
                                             ),

                      'urchinOrderTracker' => array( 'order' => array( 'type' => 'array', 'required' => true, 'default' => false )
                                             ),

                      'xmlAttributeValue' => array( 'name' => array( 'type' => 'string', 'required' => true, 'default' => false ),
                                                    'data' => array( 'type' => 'string', 'required' => true, 'default' => false )
                                             ),

                      'jsEscapedString' =>  array( 'string' => array( 'type' => 'string',
                                                   'required' => true,
                                                   'default' => '' )
                                              ),

                      'formatNumericDecimal' => array( 'number' => array( 'type' => 'string',
                                                       'required' => true,
                                                       'default' => '' ),
                                                       'places' => array( 'type' => 'string',
                                                       'required' => true,
                                                       'default' => '' )
                                              )
                      );
    }

    /*!
     \Executes the needed operator(s).
     \Checks operator names, and calls the appropriate functions.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                     &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'urchinTracker':
            {
                $operatorValue = $this->urchinTracker( );
            }
            break;

            case 'urchinTrackerHeader':
            {
                $operatorValue = $this->urchinTrackerHeader( );
            }
            break;

            case 'urchinOrderTracker':
            {
                $operatorValue = $this->urchinOrderTracker( $namedParameters['order'] );
            }
            break;

            case 'xmlAttributeValue':
            {
                $operatorValue = $this->xmlAttributeValue( $namedParameters['name'], $namedParameters['data'] );
            }
            break;

            case 'jsEscapedString':
            {
                $operatorValue = $this->jsEscapedString( $namedParameters['string'] );
            }
            break;

            case 'formatNumericDecimal':
            {
                $operatorValue = $this->formatNumericDecimal( $namedParameters['number'], $namedParameters['places'] );
            }
            break;
        }
    }

    /*!
     \Return UrchinTracker html head javascript dependancies and method call
    */
    function urchinTrackerHeader()
    {
        $ret = false;

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
            ezDebug::writeNotice( $name, 'xmlAttributeValue:name' ); */

        return $ret;
    }

    /*!
     \Return UrchinTracker javascript method call with account settings
    */
    function urchinTracker()
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
          $ret .= "  urchinTracker();\n";
          // $ret .= '--> '."\n";
          $ret .= '</script>';
        }

        /*
        if ( $debug )
            ezDebug::writeNotice( $name, 'xmlAttributeValue:name' ); */

        return $ret;
    }

    /*!
     \Quick way to get the contents of an xml string attribute.
    */
    function xmlAttributeValue( $name = false, $data, $ret = false, $debug = false )
    {
        // given string $data, will return the text string content of the $name attribute content of a given valid xml document.
        if ( $debug )
            ezDebug::writeNotice( $name, 'xmlAttributeValue:name' );

        // get information out of eZXML
        include_once('lib/ezxml/classes/ezxml.php');
        $xml = new eZXML();
        $xmlDoc = $data;

        // print_r($data);
        if ( $debug )
            ezDebug::writeNotice( $data, 'xmlAttributeValue:data' );

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
            ezDebug::writeNotice( $ret, 'xmlAttributeValue:ret' );

        return $ret;
    }

    /*!
     \Escape a string for htmlcharacters/other characters, safe for javascript. Escape Characters: , '
    */
    function jsEscapedString( $s )
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
            ezDebug::writeNotice( $e, 'jsEscapedString:ret' ); */

        return $ret;
    }

    /*!
     \Return a number, Round With Decimal Place Control
    */
    function formatNumericDecimal( $n, $p=2 )
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
     \Return UrchinOrderTracker javascript method call with account settings
    */
    function urchinOrderTracker( $order_id )
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
            $np_order_subtotal_price_ex_vat = $this->formatNumericDecimal( $order->totalExVAT() );
            $np_order_subtotal_price_inc_vat = $this->formatNumericDecimal( $order->totalIncVAT() );
            $np_order_subtotal_price_vat_sub = $np_order_subtotal_price_inc_vat - $np_order_subtotal_price_ex_vat;
            $np_order_subtotal_price_vat = $this->formatNumericDecimal( $np_order_subtotal_price_vat_sub );

            $ret .= '<textarea id="utmtrans">UTM:T|'."$order->OrderNr|$shop_name|$np_order_subtotal_price_ex_vat|$np_order_subtotal_price_vat|";

            // Fetch Shipping Total
            foreach( $order->orderItems() as $order_item )
            {
                $np_order_item_price_inc_vat = $this->formatNumericDecimal( $order_item->Price );
                $ret .= "$np_order_item_price_inc_vat";
            }

            $user_city = $this->xmlAttributeValue( 'city', $order->DataText1 );
            $user_state = $this->xmlAttributeValue( 'state', $order->DataText1 );
            $user_country = $this->xmlAttributeValue( 'country', $order->DataText1 );
            $ret .= "|$user_city|$user_state|$user_country\n";

            // Fetch Product Item Total
            foreach( $order->productItems() as $product_item )
            {
                $np_product_item_price_inc_vat = $this->formatNumericDecimal( $product_item['price_inc_vat'] );
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
            ezDebug::writeNotice( $ret, 'urchinOrderTracker:ret' ); */

        return $ret;
    }

    /// \privatesection
    var $Operators;
    var $Debug;
}

?>
