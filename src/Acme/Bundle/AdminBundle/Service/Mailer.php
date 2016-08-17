<?php
namespace Acme\Bundle\AdminBundle\Service;

/**
* Custom mailer
**/
class Mailer
{

    protected $mailer;
    protected $templating;

    public function __construct(\Swift_Mailer $mailer,$templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Send quick mail
     * @param $data
     *        		[from]
     *        		[to]
     *        		[subject]
     *        		[title]
     *        		[message]
     *        		[template]
     */

    public function quick_mail($data = array())
    {
    	//default variable
    	$data['from'] = (!isset($data['from']))?array('noreply@studiakuchenne.pl'=>'StudiaKuchenne.pl'):$data['from'];
    	$data['template'] = (!isset($data['template']))?'global.html.twig':$data['template'];

    	//genere body html
    	$body = $this->templating->render('AcmeAdminBundle:Email:'.$data['template'],$data);

    	//create object message
        $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody($body,'text/html');

        //send email
        if($this->mailer->send($message)) return true;
        return false;
    }
}
