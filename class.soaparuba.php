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

require_once('class.filereader.php');

/**
 * Check Cloud Aruba state
 * http://kb.arubacloud.com/en/partner/partner-panel/how-to-customise-the-external-services.aspx
 * http://kb.cloud.it/partner/pannello-partner/come-personalizzare-i-servizi-esterni.aspx
 * 
 * @service SoapAruba
 */
class SoapAruba{

    private $mailParameters;
    private $thresholdReached = false;
    private $creditExhausted = false;
    private $isAuthenticated = false;
    public $tempHdr = '';

    /* ------------------------------------------------------------ */
    /* ---------------- Soap Header Auth ------------------- */
    /* ------------------------------------------------------------ */

    //Soap Server setClass function passes soap header $hdr to the service class SoapAruba

    function __construct($hdr)
    {
        $this->mailParameters = FileReader::ReadFile('Configuration.yaml');

        if ($hdr!=null)
        {
            //user defined username and password
            $authUsername = $this->mailParameters['username'];
            $authPassword = $this->mailParameters['password'];

            //Clean soap header from xml tags
            $hdrCleaned = $this->cleanString($hdr);

            //Extract user and password for auth
            $username = strstr($hdrCleaned, $authPassword, true);
            $password = strstr($hdrCleaned, $authPassword);

            if($username == $authUsername && $password == $authPassword)
            {
                $this->isAuthenticated = true;
            }
        }
    }

    function cleanString($string)
    {
        $string = str_replace('<AuthHeader xmlns="http://public.ws.aruba.it/aruba/services/CreditEventReceiver/">','', $string);
        $string = str_replace('</AuthHeader>', '', $string);
        $string = str_replace('<Username>', '', $string);
        $string = str_replace('</Username>', '', $string);
        $string = str_replace('<Password>', '', $string);
        $string = str_replace('</Password>', '', $string);

        $string = preg_replace('/\s+/', '', $string);

        return $string;
    }

    function createEmailBody($eventGuid, $eventDateTime, $username, $externalUserId, $userId,
                             $accountBalance, $availableBalance, $overdraftLimit, $thresholdCredit,
                             $accountStatus, $creationDate, $rechargeDate, $plafondDate, $lastTransactionDate)
    {
        if($this->thresholdReached == true)
        {
            $body = '
            <html>
            <head>
             <title>Soglia oltrepassata</title>
            </head>
            <body>
            <table>
                <tr><td>Username</td><td>'.$username.'</td></tr>
                <tr><td>ExternalUserId</td><td>'.$externalUserId.'</td></tr>
                <tr><td>UserId</td><td>'.$userId.'</td></tr>
                <tr><td>EventGuid</td><td>'.$eventGuid.'</td></tr>
                <tr><td>EventDateTime</td><td>'.$eventDateTime.'</td></tr>
                <tr><td>Accountbalance</td><td>'.$accountBalance.'</td></tr>
                <tr><td>AvailableBalance</td><td>'.$availableBalance.'</td></tr>
                <tr><td>OverdraftLimit</td><td>'.$overdraftLimit.'</td></tr>
                <tr><td>ThresholdCredit</td><td>'.$thresholdCredit.'</td></tr>
                <tr><td>AccountStatus</td><td>'.$accountStatus.'</td></tr>
                <tr><td>CreationDate</td><td>'.$creationDate.'</td></tr>
                <tr><td>RechargeDate</td><td>'.$rechargeDate.'</td></tr>
                <tr><td>PlafondDate</td><td>'.$plafondDate.'</td></tr>
                <tr><td>LastTransactionDate</td><td>'.$lastTransactionDate.'</td></tr>
            </table>
            </body>
            </html>
            ';
            $this->thresholdReached = false;
            return $body;
        }
        else if($this->creditExhausted == true)
        {
            $body = '
            <html>
            <head>
             <title>Credito residuo esaurito</title>
            </head>
            <body>
            <table>
                <tr><td>Username</td><td>'.$username.'</td></tr>
                <tr><td>ExternalUserId</td><td>'.$externalUserId.'</td></tr>
                <tr><td>UserId</td><td>'.$userId.'</td></tr>
                <tr><td>EventGuid</td><td>'.$eventGuid.'</td></tr>
                <tr><td>EventDateTime</td><td>'.$eventDateTime.'</td></tr>
                <tr><td>Accountbalance</td><td>'.$accountBalance.'</td></tr>
                <tr><td>AvailableBalance</td><td>'.$availableBalance.'</td></tr>
                <tr><td>OverdraftLimit</td><td>'.$overdraftLimit.'</td></tr>
                <tr><td>ThresholdCredit</td><td>'.$thresholdCredit.'</td></tr>
                <tr><td>AccountStatus</td><td>'.$accountStatus.'</td></tr>
                <tr><td>CreationDate</td><td>'.$creationDate.'</td></tr>
                <tr><td>RechargeDate</td><td>'.$rechargeDate.'</td></tr>
                <tr><td>PlafondDate</td><td>'.$plafondDate.'</td></tr>
                <tr><td>LastTransactionDate</td><td>'.$lastTransactionDate.'</td></tr>
            </table>
            </body>
            </html>
            ';
            $this->creditExhausted = false;
            return $body;
        }

        return false;
    }


    /* ------------------------------------------------------------ */
    /* ------------------------------------------------------------ */

	/**
	 * ReceiveThresholdReached: this method is called when the user-defined credit threshold is reached
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
         if(!$this->isAuthenticated)
         {
             error_log('Aruba auth failed! Mail not sent',1, $this->mailParameters['mail_to']);
             return null;
         }

         $this->thresholdReached = true;
         $to      = $this->mailParameters['mail_to'];
         $subject = 'Soglia credito raggiunta';
         $message = $this->createEmailBody($EventGuid, $EventDateTime, $Username, $ExternalUserId
             ,$UserId, $Accountbalance, $AvailableBalance, $OverdraftLimit, $ThresholdCredit, $AccountStatus
             ,$CreationDate, $RechargeDate, $PlafondDate, $LastTransactionDate);

         /* To send the mail with HTML format you have to set the header Content-type */
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=".$this->mailParameters['mail_char_set']."\r\n";
        $headers .= "From: ".$this->mailParameters['mail_from']."\r\n";

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
     * ReceiveCreditExhausted: this method is called when the user terminates his credit
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
        if(!$this->isAuthenticated)
        {
            error_log('Aruba auth failed! Mail not sent' .$this->tempHdr,1, $this->mailParameters['mail_to']);
            return null;
        }


        $this->creditExhausted = true;
        $to      = $this->mailParameters['mail_to'];
        $subject = 'Credito esaurito';
        $message = $this->createEmailBody($EventGuid, $EventDateTime, $Username, $ExternalUserId
            ,$UserId, $Accountbalance, $AvailableBalance, $OverdraftLimit, $ThresholdCredit, $AccountStatus
            ,$CreationDate, $RechargeDate, $PlafondDate, $LastTransactionDate);


        /* To send the mail with HTML format you have to set the header Content-type */
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=".$this->mailParameters['mail_char_set']."\r\n";
        $headers .= "From: ".$this->mailParameters['mail_from']."\r\n";


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
