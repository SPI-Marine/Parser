<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OutlookController extends Controller
{
  public function mail() 
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());

    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();

    $messageQueryParams = array (
      // Only return Subject, ReceivedDateTime, and From fields
      "\$select" => "subject,receivedDateTime,from",
      // Sort by ReceivedDateTime, newest first
      "\$orderby" => "receivedDateTime DESC",
      // Return at most 10 results
      "\$top" => "10"
    );

    $getMessagesUrl = '/me/mailfolders/inbox/messages?'.http_build_query($messageQueryParams);
    $messages = $graph->createRequest('GET', $getMessagesUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Message::class)
                      ->execute();

    return view('mail', array(
      'username' => $user->getDisplayName(),
      'usermail' => $user->getMail(),
      'messages' => $messages
    ));
  }
  
  public function auto(){
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());

    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();

    $messageQueryParams = array (
      // Only return Subject, ReceivedDateTime, and From fields
      "\$select" => "subject,receivedDateTime,from",
      // Sort by ReceivedDateTime, newest first
      "\$orderby" => "receivedDateTime DESC",
      // Return at most 10 results
      "\$top" => "1"
    );

    // GET TOP MESSGAGE ID
    $getMessagesUrl = '/me/mailfolders/inbox/messages?'.http_build_query($messageQueryParams);
    $messages = $graph->createRequest('GET', $getMessagesUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Message::class)
                      ->execute();
    $messageId = "";
    foreach($messages as $message){
       $messageId = $message->getId();
       $fromAddress = $message->getFrom()->getEmailAddress()->getAddress();
       $subject = $message->getSubject();
    }
    
    // INDIVIDUAL MESSAGE
    $getMessageUrl = "/me/mailfolders/inbox/messages/$messageId";
    
    $message = $graph->createRequest('GET', $getMessageUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Message::class)
                      ->execute();
    
    if($message->getHasAttachments()){
        // GET ATTACHMENT
        $getAttachmentUrl = "/me/mailfolders/inbox/messages/$messageId/attachments";

        $attachments = $graph->createRequest('GET', $getAttachmentUrl)
                          ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                          ->setReturnType(Model\FileAttachment::class)
                          ->execute();
        foreach($attachments as $attachment){
            $attachment->getContentType();
            $attachment->getSize();
            $attachment->getName();
            file_put_contents("attachment.jpg", base64_decode($attachment->getContentBytes()));
        }

        
    }
    
   
  }
  
  public function message()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    
    $messageId = $_GET['message_id'];
    
    //print_r($messageId);die();
    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());
    
    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();
    
    $getMessageUrl = "/me/mailfolders/inbox/messages/$messageId";
    
    $message = $graph->createRequest('GET', $getMessageUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Message::class)
                      ->execute();
    
    $getAttachmentUrl = "/me/mailfolders/inbox/messages/$messageId/attachments";
    
    $attachments = $graph->createRequest('GET', $getAttachmentUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\FileAttachment::class)
                      ->execute();
    
    return view('message', array(
      'username' => $user->getDisplayName(),
      'usermail' => $user->getMail(),
      'message' => $message,
      'attachments' => $attachments      
    ));
    
  }
  public function attachment()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    
    $messageId = $_GET['message_id'];
    $attachmentId= $_GET['attachment_id'];
    //print_r($messageId);die();
    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());
    
    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();
    
    $getMessageUrl = "/me/mailfolders/inbox/messages/$messageId/";
    
    $message = $graph->createRequest('GET', $getMessageUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Message::class)
                      ->execute();
    
    $getAttachmentUrl = "/me/mailfolders/inbox/messages/$messageId/attachments/$attachmentId";
    
    $attachments = $graph->createRequest('GET', $getAttachmentUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\FileAttachment::class)
                      ->execute();
    
    return view('message', array(
      'username' => $user->getDisplayName(),
      'usermail' => $user->getMail(),
      'message' => $message,
      'attachments' => $attachments      
    ));
    
  }
  

  public function calendar() 
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());

    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();

    $eventsQueryParams = array (
      // Only return Subject, Start, and End fields
      "\$select" => "subject,start,end",
      // Sort by Start, oldest first
      "\$orderby" => "Start/DateTime",
      // Return at most 10 results
      "\$top" => "10"
    );

    $getEventsUrl = '/me/events?'.http_build_query($eventsQueryParams);
    $events = $graph->createRequest('GET', $getEventsUrl)
                    ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                    ->setReturnType(Model\Event::class)
                    ->execute();

    return view('calendar', array(
      'username' => $user->getDisplayName(),
      'usermail' => $user->getMail(),
      'events' => $events
    ));
  }

  public function contacts() 
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $tokenCache = new \App\TokenStore\TokenCache;

    $graph = new Graph();
    $graph->setAccessToken($tokenCache->getAccessToken());

    $user = $graph->createRequest('GET', '/me')
                  ->setReturnType(Model\User::class)
                  ->execute();

    $contactsQueryParams = array (
      // // Only return givenName, surname, and emailAddresses fields
      "\$select" => "givenName,surname,emailAddresses",
      // Sort by given name
      "\$orderby" => "givenName ASC",
      // Return at most 10 results
      "\$top" => "10"
    );

    $getContactsUrl = '/me/contacts?'.http_build_query($contactsQueryParams);
    $contacts = $graph->createRequest('GET', $getContactsUrl)
                      ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                      ->setReturnType(Model\Contact::class)
                      ->execute();

    return view('contacts', array(
      'username' => $user->getDisplayName(),
      'usermail' => $user->getMail(),
      'contacts' => $contacts
    ));
  }
}
