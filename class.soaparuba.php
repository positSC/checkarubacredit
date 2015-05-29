<?php

/*
SoapAruba - Check Aruba Cloud
Copyright (C) 2015  Posit http://www.posit.it

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, see <http://www.gnu.org/licenses/>.
*/

if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
	exit;

/**
 * Check Cloud Aruba state
 * http://kb.arubacloud.com/en/partner/partner-panel/how-to-customise-the-external-services.aspx
 * http://kb.cloud.it/partner/pannello-partner/come-personalizzare-i-servizi-esterni.aspx
 * 
 * @service SoapAruba
 */
class SoapAruba{

    /* ------------------------------------------------------------ */
    /* ---------------- PARAMETER CONFIGURATION ------------------- */
    /* ------------------------------------------------------------ */

    const MAIL_FROM     = "info@posit.it";
    const MAIL_TO       = "info@posit.it";
    const MAIL_CHAR_SET = "iso-8859-1";
    const USERNAME      = "posit";
    const PASSWORD      = "pwdPassPosit!!";

    /* ------------------------------------------------------------ */
    /* ------------------------------------------------------------ */

	/**
	 * ReceiveThresholdReached: questo metodo viene richiamato nel caso in cui venga oltrepassata la soglia del credito definita dall'utente
	 * @param  string $EventGuid
     * @param  string $EventDateTime
     * @param  string $Username
     * @param  string $ExternalUserId
     * @param  string $UserId
     * @param  string $Accountbalance
     * @param  string $AvailableBalance
     * @param  string $OverdraftLimit
     * @param  string $ThresholdCredit
     * @param  string $AccountStatus
     * @param  string $CreationDate
     * @param  string $RechargeDate
     * @param  string $PlafondDate
     * @param  string $LastTransactionDat
	 */
	 function  ReceiveThresholdReached(
                $EventGuid=NULL
                ,$EventDateTime=NULL
                ,$Username=NULL
                ,$ExternalUserId=NULL
                ,$UserId=NULL
                ,$Accountbalance=NULL
                ,$AvailableBalance=NULL
                ,$OverdraftLimit=NULL
                ,$ThresholdCredit=NULL
                ,$AccountStatus=NULL
                ,$CreationDate=NULL
                ,$RechargeDate=NULL
                ,$PlafondDate=NULL
                ,$LastTransactionDate=NULL
                )
     {

         $to      = self::MAIL_TO;
         $subject = 'Soglia cloud oltrepassata';
         $message = '
            <html>
            <head>
             <title>Soglia oltrepassata</title>
            </head>
            <body>
            <table>
                <tr><td>Username</td><td>'.$Username.'</td></tr>
                <tr><td>ExternalUserId</td><td>'.$ExternalUserId.'</td></tr>
                <tr><td>UserId</td><td>'.$UserId.'</td></tr>
                <tr><td>EventGuid</td><td>'.$EventGuid.'</td></tr>
                <tr><td>EventDateTime</td><td>'.$EventDateTime.'</td></tr>
                <tr><td>Accountbalance</td><td>'.$Accountbalance.'</td></tr>
                <tr><td>AvailableBalance</td><td>'.$AvailableBalance.'</td></tr>
                <tr><td>OverdraftLimit</td><td>'.$OverdraftLimit.'</td></tr>
                <tr><td>ThresholdCredit</td><td>'.$ThresholdCredit.'</td></tr>
                <tr><td>AccountStatus</td><td>'.$AccountStatus.'</td></tr>
                <tr><td>CreationDate</td><td>'.$CreationDate.'</td></tr>
                <tr><td>RechargeDate</td><td>'.$RechargeDate.'</td></tr>
                <tr><td>PlafondDate</td><td>'.$PlafondDate.'</td></tr>
                <tr><td>LastTransactionDate</td><td>'.$LastTransactionDate.'</td></tr>
            </table>
            </body>
            </html>
            ';

        /* Per inviare email in formato HTML, si deve impostare l'intestazione Content-type. */
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=".self::MAIL_CHAR_SET."\r\n";
        $headers .= "From: ".self::MAIL_FROM."\r\n";

        if ( mail($to, $subject, $message, $headers) )
        {
            error_log(" ReceiveThresholdReached: mail inviata");
        }
        else
        {
            error_log(" ReceiveThresholdReached: mail non inviata");
        }
        return TRUE;
     }
    /**
     * ReceiveCreditExhausted: questo metodo viene richiamato quando l'utente finisce il credito disponibile
     * @param string $EventGuid
     * @param string $EventDateTime
     * @param string $Username
     * @param string $ExternalUserId
     * @param string $UserId
     * @param string $Accountbalance
     * @param string $AvailableBalance
     * @param string $OverdraftLimit
     * @param string $ThresholdCredit
     * @param string $AccountStatus
     * @param string $CreationDate
     * @param string $RechargeDate
     * @param string $PlafondDate
     * @param string $LastTransactionDate
     */
    function  ReceiveCreditExhausted(
            $EventGuid
        , $EventDateTime
        , $Username
        , $ExternalUserId
        , $UserId
        , $Accountbalance
        , $AvailableBalance
        , $OverdraftLimit
        , $ThresholdCredit
        , $AccountStatus
        , $CreationDate
        , $RechargeDate
        , $PlafondDate
        , $LastTransactionDate)
    {
        $to      = self::MAIL_TO;
        $subject = 'Credito disponibile esaurito';
        $message = '
            <html>
            <head>
             <title>Credito disponibile esaurito</title>
            </head>
            <body>
            <table>
                <tr><td>Username</td><td>'.$Username.'</td></tr>
                <tr><td>ExternalUserId</td><td>'.$ExternalUserId.'</td></tr>
                <tr><td>UserId</td><td>'.$UserId.'</td></tr>
                <tr><td>EventGuid</td><td>'.$EventGuid.'</td></tr>
                <tr><td>EventDateTime</td><td>'.$EventDateTime.'</td></tr>
                <tr><td>Accountbalance</td><td>'.$Accountbalance.'</td></tr>
                <tr><td>AvailableBalance</td><td>'.$AvailableBalance.'</td></tr>
                <tr><td>OverdraftLimit</td><td>'.$OverdraftLimit.'</td></tr>
                <tr><td>ThresholdCredit</td><td>'.$ThresholdCredit.'</td></tr>
                <tr><td>AccountStatus</td><td>'.$AccountStatus.'</td></tr>
                <tr><td>CreationDate</td><td>'.$CreationDate.'</td></tr>
                <tr><td>RechargeDate</td><td>'.$RechargeDate.'</td></tr>
                <tr><td>PlafondDate</td><td>'.$PlafondDate.'</td></tr>
                <tr><td>LastTransactionDate</td><td>'.$LastTransactionDate.'</td></tr>
            </table>
            </body>
            </html>
            ';


        /* Per inviare email in formato HTML, si deve impostare l'intestazione Content-type. */
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=".self::MAIL_CHAR_SET."\r\n";
        $headers .= "From: ".self::MAIL_FROM."\r\n";

        if ( mail($to, $subject, $message, $headers) )
        {
            error_log(" ReceiveThresholdReached: mail inviata");
        }
        else
        {
            error_log(" ReceiveThresholdReached: mail non inviata");
        }
        return TRUE;
    }

}
