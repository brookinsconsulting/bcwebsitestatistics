<?php
//
// Definition of GoogleAnalyticsOperators autoload
//
// Created on: <14-05-2007 08:42:02 gb>
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

// eZDebug::writeWarning( 'GoogleAnalyticsOperators::autoload : load template operator' );

// Operator autoloading
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/googleanalytics/autoloads/googleanalyticsoperators.php',
                                    'class' => 'GoogleAnalyticsOperators',
                                    'operator_names' => array( 'bc_ga_urchin', 'bc_ga_urchinHeader', 'bc_ga_urchinOrder', 'xmlAttributeValue', 'jsEscapedString', 'formatNumericDecimal' ) );

?>