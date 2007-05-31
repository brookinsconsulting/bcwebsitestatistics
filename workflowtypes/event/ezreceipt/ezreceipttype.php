<?php
//
// Definition of eZRecieptType class
//
// Created on: <03-02-2007 19:42:02 gb>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//
// SOFTWARE NAME: Google Analytics
// COPYRIGHT NOTICE: Copyright (C) 2001-2007 Brookins Consulting
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0 of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301,  USA.
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file ezreceipttype.php
 */

/*!
  \class Reciept ezreceipttype.php
  \brief The event Reciept handles the display of a order reciept module vi
ew to a user for dispay, save, print and to also display custom google analytics javascri
pt to transmit order purchase details (only durring the final order process) to google.
*/

define( 'EZ_WORKFLOW_TYPE_RECEIPT_ID', 'ezreceipt' );

class eZReceiptType extends eZWorkflowEventType
{
    /*!
     Constructor
    */
    function eZReceiptType()
    {
        $this->eZWorkflowEventType( EZ_WORKFLOW_TYPE_RECEIPT_ID, ezi18n( 'kernel/workflow/event', "BC Google Analytics - Order Statistics Submission and Order Completed View" ) );

        $this->setTriggerTypes( array( 'shop' => array(
                                'checkout' => array (
                                'after' ) ) ) );
    }

    function execute( &$process, &$event )
    {
        // Fetch request variables
        $http =& eZHTTPTool::instance();
        $requestUri = eZSys::requestURI();

        // Fetch custom settings
        $ini =& eZINI::instance( 'bcgoogleanalytics.ini' );
        $test = $ini->variable( 'BCGoogleAnalyticsWorkflow', 'TestMode' ) == 'enabled' ? true : false;
        $debug = $ini->variable( 'BCGoogleAnalyticsWorkflow', 'DebugMode' ) == 'enabled' ? true : false;
        $urchin = $ini->variable( 'BCGoogleAnalyticsWorkflow', 'Urchin' );
        $udn = $ini->variable( 'BCGoogleAnalyticsWorkflow', 'HostName' );

        // Setting to control submission of information
        // to google via client side script (javascript)

        if ( $ini->hasVariable( 'BCGoogleAnalyticsWorkflow', 'OrderSubmitToGoogle' ) )
        {
          $settingSubmitToGoogle = $ini->variable( 'BCGoogleAnalyticsWorkflow', 'OrderSubmitToGoogle' ) == 'enabled' ? true : false;
        }
        else
        {
          $settingSubmitToGoogle = false;
        }

        if ( $settingSubmitToGoogle == true )
        {
          // Add hook to trigger template override of pagelayout.tpl
          include_once( 'kernel/common/eztemplatedesignresource.php' );
          $res =& eZTemplateDesignResource::instance();
          $res->setKeys( array( array( 'googleanalytics', '1' ) ) );

          // Template Settings
          $tpl_name = "design:google/analytics/order.tpl";
          $process->Template = array(
                               'templateName' => $tpl_name,
                               'templateVars' => array( 'request_uri' => $requestUri )
                               );

          return EZ_WORKFLOW_TYPE_STATUS_FETCH_TEMPLATE_REPEAT;
        }
        else
        {
            return EZ_WORKFLOW_TYPE_STATUS_ACCEPTED;
        }
    }
}

eZWorkflowEventType::registerType( EZ_WORKFLOW_TYPE_RECEIPT_ID, "ezreceipttype" );

?>