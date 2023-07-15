<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use \Mpdf\Mpdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class QrcodeController extends BaseController
{

    public function create(){
        return view('create_pdf');
    }

    // public function store() {
    //     $userModel = new UserModel();
    //     $data = [
    //         'name' => $this->request->getVar('name'),
    //         'email'  => $this->request->getVar('email'),
    //         'country'  => $this->request->getVar('country'),
    //     ];
    //     //print_r($data); die();
    //     $userModel->insert($data);
    //     return $this->response->redirect(site_url('/users-list'));
    // }
    

    public function generateqrcode()
    {
        // Load the mPDF library
       require_once '../vendor/autoload.php';      


        $writer = new PngWriter();

                    $name = $this->request->getVar('name');
                    $email  = $this->request->getVar('email');
                    $country  = $this->request->getVar('country');
                    $data = 'this is ' .$name. '. My email id is '.$email.' and I am a citizen of '.$country;
        // Create QR code
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
            $result = $writer->write($qrCode);
            $result->saveToFile(APPPATH .'/qrcode/qrcode.png');
            //print_r($result);
    
       
        // Send the email
        $this->sendEmailWithAttachment($content);
    }

    private function sendEmailWithAttachment($attachmentData)
    {
        
        
        // Load the email library
        //$this->load->library('email');
        $email = \Config\Services::email();

        // Email configuration
        $config = Array(
          'protocol' => 'smtp',
          'smtp_host' => 'sandbox.smtp.mailtrap.io',
          'smtp_port' => 2525,
          'smtp_user' => 'cfa87fd337de98',
          'smtp_pass' => 'fee53581e1ed05',
          'crlf' => "\r\n",
          'newline' => "\r\n"
        );
        // Initialize the email
        $email->initialize($config);



        // Set the email details
        $email->setFrom('92vky92@gmail.com','vineet');
        $email->setTo('vineetkumar1905@gmail.com');
        $email->setReplyTo('92vky92@gmail.com', 'vineet');
        $email->setSubject('Qrcode Attachment');
        $email->setMessage('Please find the attached QrCode.');

        // Attach the PDF to the email
        //$email->attach($attachmentData, 'attachment.pdf', 'application/pdf');
        $qrcode=APPPATH . 'qrcode/qrcode.png';
        //print_r($pdf_path); die();
        $email->attach($qrcode);

        // Send the email
        //$email->send();

        if($email->send(true))
        {
            echo "Mail Sent Successfully";
        }
        else
        {
            echo "Failed to send email";
            $email->printDebugger(['headers']);     
        }
        
    }

}
